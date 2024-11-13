<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Document;
use App\Models\documentVersion;
use App\Models\Starred_document;
use Gemini\Laravel\Facades\Gemini;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;
use PhpOffice\PhpWord\IOFactory; // For Word documents
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetIOFactory;

class FileController extends Controller
{
    public function index($uuid)
    {
        $file = Document::select('*', 'documents.created_at as created_at', 'document_versions.uuid as uuid')
            ->join('document_versions', 'document_versions.uuid', '=', 'documents.latest_version_guid')
            ->join('users', 'users.id', '=', 'documents.upload_by')
            ->where('document_versions.uuid', '=', $uuid)
            ->first();

        // $version = documentVersion::join('documents', 'documents.id', '=', 'document_versions.doc_guid')
        //     ->where('documents.latest_version_guid', '=', $uuid)
        //     ->get();

        $version = documentVersion::select('*', 'document_versions.uuid as uuid', 'document_versions.created_at as created_at')
            ->join('documents', 'documents.id', '=', 'document_versions.doc_guid')
            ->join('users', 'users.id', '=', 'document_versions.created_by')
            ->where('documents.latest_version_guid', '=', $uuid)
            ->orderBy('document_versions.created_at', 'desc')
            ->get();

        // Check if the document exists
        if (!$file) {
            return redirect()->route('fileManager.index')->with('error', 'File not found or has been deleted.');
        }

        $audit_logs = AuditLog::select('*', 'audit_logs.created_at as created_at')
            ->join('documents', 'documents.id', '=', 'audit_logs.doc_guid')
            ->join('users', 'users.id', '=', 'documents.upload_by')
            ->where('documents.latest_version_guid', $uuid)
            ->orderBy('audit_logs.created_at', 'desc')
            ->get();


        // Split the doc_summary into lines (if applicable)
        $doc_summary = explode("\n", $file->doc_summary);
        return view('admin.file-manager.detail-page', [
            'data' => $file,
            'doc_summary' => $doc_summary,
            'version' => $version,
            'audit_logs' => $audit_logs,
        ]);
    }

    public function update(Request $request, $uuid)
    {

        // $file = Document::where('uuid', '=', $uuid)->first();
        $file = Document::where('latest_version_guid', '=', $uuid)->first();

        // Process the doc_keyword field to save as comma-separated string
        $keywords = $request->input('doc_keyword');
        $processed_keywords = '';

        if ($keywords) {
            $tags = json_decode($keywords, true); // Decode JSON into array
            $processed_keywords = implode(', ', array_column($tags, 'value')); // Convert to comma-separated string
        }

        if (!$file) {
            return response()->json(['error' => 'File not found'], 404);
        }

        $file->update([
            'doc_title' => request('doc_title'),
            'doc_description' => request('doc_description'),
            'doc_summary' => request('doc_summary'),
            'doc_author' => request('doc_author'),
            'doc_keyword' => $processed_keywords,
            'version_limit' => request('version_limit'),
        ]);

        // Return success response
        return redirect()->back()->with(['success' => 'File details updated successfully']);
    }

    public function generate_summary($uuid)
    {
        // // Find the document by UUID
        $document = documentVersion::where('uuid', '=', $uuid)->first();

        if (!$document) {
            return response()->json(['success' => false, 'message' => 'Document not found'], 404);
        }

        // // Get the doc_content (the text to summarize)
        $docContent = $document->doc_content;

        if (empty($docContent)) {
            return response()->json(['success' => false, 'message' => 'Document content is empty'], 400);
        }

        try {
            // Generate summary using Google Gemini package
            $result = Gemini::geminiPro()->generateContent('Summarize the following text: ', $docContent);
            $summary = $result->text();

            // Return the summary as a JSON response
            return response()->json([
                'success' => true,
                'summary' => $summary
            ]);
        } catch (\Exception $e) {
            // Handle any errors with more detail
            return response()->json(['success' => false, 'message' => 'Sorry, Unable to generate Summary'], 500);
        }
    }

    public function add_version(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'file' => 'required|mimes:jpg,jpeg,png,pdf,doc,docx,pptx,xlsx,csv|max:102400', // Adjust MIME types as needed
            'change_title' => 'required|string',
            'change_description' => 'required|string',
        ]);

        // Find the document by UUID
        $document = Document::where('latest_version_guid', $request->id)->first();

        AuditLog::create([
            'action' => 'Created',
            'model' => 'New version',
            'doc_guid' => $document->id,
            'changes' => $document->doc_title,
            'user_guid' => Auth::user()->id,
            'ip_address' => request()->ip(),
        ]);

        if (!$document) {
            return redirect()->back()->with('error', 'Document not found.');
        }

        if ($document->version_limit && $document->documentVersions()->count() >= $document->version_limit) {
            return response()->json(['success' => false, 'error' => 'Version limit reached!']);
        }

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $uniqueFileName = time() . '_' . uniqid() . '.' . $extension;
            $fileSize = $file->getSize(); // Size in bytes

            // Define folder based on file extension
            $folder = $this->getFolderByFileType($extension);

            // Store the file in the corresponding folder in 'storage/app/uploads/{folder}'
            // $filePath = $file->storeAs('uploads/' . $folder, $uniqueFileName, 'public');

            $filePath = 'uploads/' . $folder . '/' . $uniqueFileName;
            Storage::put($filePath, file_get_contents($file));

            // Get the latest version number and increment it
            $lastVersion = DocumentVersion::where('doc_guid', $document->id)
                ->orderBy('created_at', 'desc')
                ->first();

            // Determine the next version number
            if ($lastVersion) {
                // Increment the last version's number
                $lastVersionNumber = $lastVersion->version_number; // e.g., "v1.0"
                $newVersionNumber = $this->incrementVersionNumber($lastVersionNumber);
            } else {
                // Default to "v1.0" if this is the first version
                $newVersionNumber = 'v1.0';
            }

            $docContent = null;

            // If the file is a PDF, extract its content
            if ($extension === 'pdf') {
                $pdfParser = new Parser();
                $fileContent = file_get_contents($file);

                $pdf = $pdfParser->parseContent($fileContent);
                $docContent = $pdf->getText(); // Extract the text from the PDF
            }

            // If the file is a DOCX, extract its content using the method defined below
            if ($extension === 'docx') {
                $docContent = $this->extractTextFromDocx($file); // Extract text from DOCX
            }

            // Handle Excel file
            if (in_array($extension, ['xlsx', 'xls', 'csv'])) {
                $docContent = $this->extractTextFromExcel($file); // Extract text from Excel
            }

            // Check if OCR content was sent for images
            if (in_array($extension, ['jpeg', 'jpg', 'png']) && $request->has('ocr_content')) {
                $docContent = $request->input('ocr_content'); // Use the extracted OCR content from the request
            }

            // Create a new version entry
            $newVersion = DocumentVersion::create([
                'version_number' => $newVersionNumber,
                'change_title' => $request->change_title,
                'change_description' => $request->change_description,
                'file_path' => $filePath,
                'file_size' => $fileSize,
                'created_by' => Auth::user()->id,
                'doc_guid' => $document->id,
                'doc_content' => $docContent, // Store the extracted document content
            ]);

            // Update the document's latest version reference
            $document->latest_version_guid = $newVersion->uuid;
            $document->save();

            return response()->json(['uuid' => $newVersion->uuid]);
        }

        return redirect()->back()->with('error', 'No file uploaded.');
    }

    //extract docx
    public function extractTextFromDocx($filePath)
    {
        $phpWord = IOFactory::load($filePath);
        $text = '';

        foreach ($phpWord->getSections() as $section) {
            $text .= $this->extractTextFromSection($section);
        }

        return $text;
    }

    //extract docx
    private function extractTextFromSection($element)
    {
        $text = '';

        foreach ($element->getElements() as $childElement) {
            if ($childElement instanceof TextRun) {
                $text .= $this->extractTextFromTextRun($childElement);
            } elseif ($childElement instanceof Text) {
                $text .= $childElement->getText();
            }
        }

        return $text;
    }

    //extract docx
    private function extractTextFromTextRun(TextRun $textRun)
    {
        $text = '';

        foreach ($textRun->getElements() as $textElement) {
            if ($textElement instanceof Text) {
                $text .= $textElement->getText();
            }
        }

        return $text;
    }

    // extract excel
    public function extractTextFromExcel($filePath)
    {
        $spreadsheet = SpreadsheetIOFactory::load($filePath);
        $text = '';

        // Loop through each sheet in the spreadsheet
        foreach ($spreadsheet->getAllSheets() as $sheet) {
            // Loop through each row in the sheet
            foreach ($sheet->getRowIterator() as $row) {
                $cellIterator = $row->getCellIterator();
                $cellIterator->setIterateOnlyExistingCells(false); // Loop through all cells in the row

                foreach ($cellIterator as $cell) {
                    $text .= $cell->getValue() . ' '; // Append the cell's value
                }

                $text .= "\n"; // Newline for each row
            }
        }

        return $text;
    }

    /**
     * Increment the version number (e.g., v1.0 -> v1.1 or v2.0 -> v2.1)
     * 
     * @param string $versionNumber
     * @return string
     */
    private function incrementVersionNumber($versionNumber)
    {
        // Assuming version format is like "v1.0" or "v2.3"
        $parts = explode('.', str_replace('v', '', $versionNumber)); // Remove 'v' and split by '.'

        if (count($parts) == 2) {
            $parts[0] = intval($parts[0]) + 1; // Increment the major version
            $parts[1] = 0; // Reset the minor version to 0
        } else {
            // Fallback in case of unexpected version format
            return 'v1.0';
        }

        return 'v' . $parts[0] . '.' . $parts[1]; // Return incremented version number
    }


    /**
     * Get the folder name based on file type (extension)
     * 
     * @param string $extension
     * @return string
     */
    private function getFolderByFileType($extension)
    {
        // Map extensions to specific folder names
        $folders = [
            'pdf' => 'pdf',
            'doc' => 'doc',
            'docx' => 'docx',
            'jpg' => 'images',
            'jpeg' => 'images',
            'png' => 'images',
            'pptx' => 'pptx',
            'csv' => 'csv',
            'xlsx' => 'xlsx',
        ];

        // Return the appropriate folder or default to 'other' if not found
        return isset($folders[$extension]) ? $folders[$extension] : 'other';
    }

    public function destroy_file($uuid)
    {
        // Find the document version by UUID
        $documentVersion = documentVersion::where('uuid', '=', $uuid)->first();

        // Find the document by latest version GUID
        $document = Document::where('latest_version_guid', '=', $uuid)->first();

        AuditLog::create([
            'action' => 'Deleted',
            'model' => 'File version',
            'doc_guid' => $document->id,
            'changes' => $document->doc_title,
            'user_guid' => Auth::user()->id,
            'ip_address' => request()->ip(),
        ]);

        // Check if the document and version exist
        if ($documentVersion && $document) {
            // Get all versions of the document using the document's doc_guid
            $allVersions = documentVersion::where('doc_guid', '=', $documentVersion->doc_guid)->orderBy('created_at', 'desc')->get();

            // If there is only one version, proceed to delete both the document and the version
            if ($allVersions->count() === 1) {
                // Delete the only version's file
                $filePath = $documentVersion->file_path;
                if (Storage::exists($filePath)) {
                    Storage::delete($filePath); // Delete the file from 'public' disk
                }

                // Delete the version and the document

                $documentVersion = documentVersion::where('uuid', '=', $uuid)->delete();
                Starred_document::where('doc_guid', $document->id)->delete();

                $document = Document::where('latest_version_guid', '=', $uuid)->delete();

                return response()->json(['success' => true, 'message' => 'File and document deleted successfully']);
            }

            // Otherwise, delete the current version and update the document's latest_version_guid
            $filePath = $documentVersion->file_path;
            if (Storage::exists($filePath)) {
                Storage::delete($filePath); // Delete the file from 'public' disk
            }


            // Delete the current version record from the database
            $documentVersion->delete();

            // Get the new latest version (the one created just before the current one)
            $previousVersion = documentVersion::where('doc_guid', '=', $documentVersion->doc_guid)
                ->orderBy('created_at', 'desc')
                ->first();

            // Update the latest_version_guid in the Document model to the new latest version's UUID
            if ($previousVersion) {
                $document->latest_version_guid = $previousVersion->uuid;
                $document->save();
            }

            // Redirect back with a success message
            return response()->json([
                'success' => true,
                'message' => 'Version deleted successfully, latest version updated',
                'uuid' => $previousVersion->uuid

            ]);
        }

        // Return error if document or version not found
        return response()->json(['success' => false, 'message' => 'File or document not found']);
    }
}
