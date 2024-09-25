<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Document;
use App\Models\documentVersion;
use App\Models\Folder;
use App\Models\Stat;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;
use PhpOffice\PhpWord\IOFactory; // For Word documents
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetIOFactory;
use Illuminate\Support\Facades\DB;

class FileManagerController extends Controller
{
    public function index()
    {
        $folders = Folder::with(['children', 'documents'])
            ->select('*', 'folders.uuid as uuid', 'folders.id as id')
            ->whereNull('parent_folder_guid')
            ->join('users', 'users.id', '=', 'folders.created_by')
            ->get();

        // Fetch root-level documents where folder_guid is null
        $rootDocuments = Document::select('*', 'documents.uuid as uuid', 'documents.id as id')
            ->join('users', 'users.id', '=', 'documents.upload_by')
            ->get();

        return view('admin.file-manager.file-manager', [
            'folders' => $folders,
            'documents' => $rootDocuments
        ]);
    }


    public function show_folder($uuid)
    {

        // Fetch folder with its children and documents
        $folder = Folder::with(['children', 'documents'])->where('uuid', '=', $uuid)->first();
        $path = $this->getFolderPath($folder);

        // $document = Document::where('folder_guid', '=', $uuid)->get();
        $document = Document::select('*', 'documents.id as id')
            ->join('folders', 'folders.id', '=', 'documents.folder_guid')
            ->where('folders.uuid', '=', $uuid)
            ->get();

        return view('admin.file-manager.file-manager-item', [
            'folder' => $folder,
            'path' => $path,
            'documents' => $document,
        ]);
    }
    public function getFolderPath(Folder $folder)
    {
        $path = [];

        // Loop to traverse back to the root folder
        while ($folder) {
            array_unshift($path, $folder); // Prepend to maintain correct order
            $folder = $folder->parent;
        }

        return $path;
    }

    public function create(Request $request)
    {
        $request->validate([
            'new_folder_name' => 'required',
        ]);

        $folder = new Folder();
        $folder->folder_name = $request->new_folder_name;
        $folder->parent_folder_guid = $request->new_folder_id;
        $folder->created_by = Auth::user()->id;
        $folder->org_guid = Auth::user()->org_guid;
        $folder->is_meeting = 'N';
        $folder->save();

        $folderCount = Folder::count();

        // Get today's stat entry (or create a new one if it doesn't exist)
        $todayStats = Stat::whereDate('created_at', Carbon::today())->first();

        if (!$todayStats) {
            // Create a new stat entry for today if it doesn't exist
            Stat::create([
                'folder_count' => $folderCount,
            ]);
        } else {
            // Update today's counts if the entry already exists
            $todayStats->update([
                'folder_count' => $folderCount,
            ]);
        }

        return redirect()->back();
    }

    public function destroy($id)
    {
        // Find the folder by ID
        $folder = Folder::find($id);

        if ($folder) {
            // Recursively delete all documents, files, and subfolders
            $this->deleteFolderContents($folder);

            // Finally, delete the folder itself
            $folder->delete();

            $folderCount = Folder::count();

            // Get today's stat entry (or create a new one if it doesn't exist)
            $todayStats = Stat::whereDate('created_at', Carbon::today())->first();

            if (!$todayStats) {
                // Create a new stat entry for today if it doesn't exist
                Stat::create([
                    'folder_count' => $folderCount,
                ]);
            } else {
                // Update today's counts if the entry already exists
                $todayStats->update([
                    'folder_count' => $folderCount,
                ]);
            }

            return response()->json(['success' => true, 'message' => 'Folder and its contents deleted successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Folder not found']);
    }

    private function deleteFolderContents($folder)
    {
        // Get all documents related to this folder
        $documents = Document::join('document_versions', 'document_versions.doc_guid', '=', 'documents.id')
            ->where('documents.folder_guid', '=', $folder->id)
            ->get();

        foreach ($documents as $doc) {
            $filePath = $doc->file_path;

            // Delete the file from storage if it exists
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            // Delete associated document versions
            DocumentVersion::where('doc_guid', $doc->id)->delete();

            // Delete the document itself
            $doc->delete();
        }

        // Find all subfolders of the current folder
        $subfolders = Folder::where('parent_folder_guid', $folder->id)->get();

        // Recursively delete each subfolder and its contents
        foreach ($subfolders as $subfolder) {
            $this->deleteFolderContents($subfolder); // Recursive call to delete subfolder contents
            $subfolder->delete(); // Delete the subfolder itself
        }
    }

    public function rename(Request $request, $id)
    {
        $folder = Folder::find($id);

        if (!$folder) {
            return response()->json(['success' => false, 'message' => 'Folder not found.']);
        }

        $folder->folder_name = $request->input('new_folder_name');
        $folder->save();

        return response()->json(['success' => true, 'message' => 'Folder renamed successfully']);
    }

    public function deleteSelected(Request $request)
    {
        // Get the array of selected folder IDs from the request
        $ids = $request->input('ids');

        if (!$ids || !is_array($ids) || empty($ids)) {
            return response()->json(['success' => false, 'message' => 'No folders selected for deletion']);
        }

        // Find all folders that match the selected IDs
        $folders = Folder::whereIn('id', $ids)->get();

        // AuditLog::create([
        //     'action' => "Deleted",
        //     'model' => 'Folder',
        //     'changes' => json_encode($folders),
        //     'user_guid' => Auth::user()->id,
        //     'ip_address' => request()->ip(),
        // ]);

        foreach ($folders as $folder) {
            // Recursively delete folder contents (files, documents, subfolders)
            $this->deleteFolderContents($folder);

            // Finally, delete the folder itself
            $folder->delete();
        }

        $folderCount = Folder::count();

        // Get today's stat entry (or create a new one if it doesn't exist)
        $todayStats = Stat::whereDate('created_at', Carbon::today())->first();

        if (!$todayStats) {
            // Create a new stat entry for today if it doesn't exist
            Stat::create([
                'folder_count' => $folderCount,
            ]);
        } else {
            // Update today's counts if the entry already exists
            $todayStats->update([
                'folder_count' => $folderCount,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Selected folders and their contents deleted successfully']);
    }

    public function file_deleteSelected(Request $request)
    {
        // Get the array of selected document IDs
        $ids = $request->input('ids');

        // Fetch all document versions for the given IDs
        $documents = documentVersion::join('documents', 'documents.id', '=', 'document_versions.doc_guid')
            ->whereIn('documents.id', $ids)
            ->get();

        // Iterate over each document
        foreach ($documents as $document) {
            $filePath = $document->file_path;
            AuditLog::create([
                'action' => "Deleted",
                'model' => 'File',
                'changes' => $document->doc_title,
                'user_guid' => Auth::user()->id,
                'ip_address' => request()->ip(),
            ]);
            // Assuming $filePath is relative to the 'public' disk (e.g., 'uploads/myfile.pdf')
            if (Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath); // Delete the file from 'public' disk
            }

            // Find the document in the documents table and delete it
            Document::where('id', $document->doc_guid)->delete();
        }

        // Get current counts
        $fileCount = Document::count();

        // Get today's stat entry (or create a new one if it doesn't exist)
        $todayStats = Stat::whereDate('created_at', Carbon::today())->first();

        if (!$todayStats) {
            // Create a new stat entry for today if it doesn't exist
            Stat::create([
                'file_count' => $fileCount,
            ]);
        } else {
            // Update today's counts if the entry already exists
            $todayStats->update([
                'file_count' => $fileCount,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Selected files deleted successfully']);
    }

    public function destroy_file($id)
    {

        $document = documentVersion::join('documents', 'documents.id', '=', 'document_versions.doc_guid')
            ->where('documents.id', '=', $id)
            ->first();

        $documentId = Document::find($id);

        // Check if the document exists
        if ($document) {
            // Get the file path in storage
            // Get all versions of the document using the document's ID or any identifier that links to versions
            $allVersions = documentVersion::where('doc_guid', '=', $documentId->id)->get();

            // Loop through all versions and delete associated files
            foreach ($allVersions as $version) {
                $filePath = $version->file_path;

                // Assuming $filePath is relative to the 'public' disk (e.g., 'uploads/myfile.pdf')
                if (Storage::disk('public')->exists($filePath)) {
                    Storage::disk('public')->delete($filePath); // Delete the file from 'public' disk
                }

                // Delete the version record from the database
                $version->delete();
            }

            // Now delete the main document record from the database
            $documentId->delete();

            // Get current counts
            $fileCount = Document::count();

            // Get today's stat entry (or create a new one if it doesn't exist)
            $todayStats = Stat::whereDate('created_at', Carbon::today())->first();

            if (!$todayStats) {
                // Create a new stat entry for today if it doesn't exist
                Stat::create([
                    'file_count' => $fileCount,
                ]);
            } else {
                // Update today's counts if the entry already exists
                $todayStats->update([
                    'file_count' => $fileCount,
                ]);
            }

            // Redirect back with a success message
            return response()->json(['success' => true, 'message' => 'File deleted successfully']);
        }

        return response()->json(['success' => false, 'message' => 'File not found']);
    }

    // upload and extract file

    public function upload_file(Request $request)
    {
        // Validate the file input
        $request->validate([
            'file' => 'required|mimes:jpg,jpeg,png,pdf,doc,docx,pptx,xlsx,csv|max:1024', // 1MB Max
        ]);

        // Handle the uploaded file
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $extension = $file->getClientOriginalExtension();
            $uniqueFileName = time() . '_' . uniqid() . '.' . $extension;
            $originalName = $file->getClientOriginalName();
            $fileSize = $file->getSize(); // Size in bytes


            // Define folder based on file extension
            $folder = $this->getFolderByFileType($extension); // Separate function to get the folder

            // Store the file in the corresponding folder in 'storage/app/uploads/{folder}'
            $filePath = $file->storeAs('uploads/' . $folder, $uniqueFileName, 'public');

            $folder_id = $request->folder_id;

            // Initialize the document content variable
            $docContent = null;

            // If the file is a PDF, extract its content
            if ($extension === 'pdf') {
                $pdfParser = new Parser();
                $pdf = $pdfParser->parseFile(public_path('storage/' . $filePath));
                $docContent = $pdf->getText(); // Extract the text from the PDF
            }

            // If the file is a DOCX, extract its content using the method defined below
            if ($extension === 'docx') {
                $docContent = $this->extractTextFromDocx(public_path('storage/' . $filePath)); // Extract text from DOCX
            }


            // Handle Excel file
            if (in_array($extension, ['xlsx', 'xls', 'csv'])) {
                $docContent = $this->extractTextFromExcel(public_path('storage/' . $filePath)); // Extract text from Excel
            }

            // Check if OCR content was sent for images
            if (in_array($extension, ['jpeg', 'jpg', 'png']) && $request->has('ocr_content')) {
                $docContent = $request->input('ocr_content'); // Use the extracted OCR content from the request
            }

            $documentVersion = documentVersion::create([
                'doc_guid' => null,
                'file_path' => $filePath,
                'file_size' => $fileSize,
                'doc_content' => $docContent, // Store the extracted document content
                'created_by' => Auth::user()->id,
                'version_number' => 'v1.0',
                'change_description' => 'Not set',
            ]);

            // Store file information in the database
            $newFile = Document::create([
                'doc_name' => $uniqueFileName,
                'doc_title' => $originalName,
                'folder_guid' => $folder_id,
                'doc_type' => $folder,
                'upload_by' => Auth::user()->id,
                'org_guid' => Auth::user()->org_guid,
                'doc_description' => 'Not set',
                'doc_summary' => 'Not set',
                'doc_author' => 'Not set',
                'doc_keyword' => 'Not set',
                'version_limit' => '5',
                'latest_version_guid' => $documentVersion->uuid,
            ]);

            $documentVersion->doc_guid = $newFile->id;
            $documentVersion->save();

            // Get current counts
            $fileCount = Document::count();

            // Get today's stat entry (or create a new one if it doesn't exist)
            $todayStats = Stat::whereDate('created_at', Carbon::today())->first();

            if (!$todayStats) {
                // Create a new stat entry for today if it doesn't exist
                Stat::create([
                    'file_count' => $fileCount,
                ]);
            } else {
                // Update today's counts if the entry already exists
                $todayStats->update([
                    'file_count' => $fileCount,
                ]);
            }



            return back()->with('success', 'File uploaded successfully.');
        }

        return back()->with('error', 'No file selected.');
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
}
