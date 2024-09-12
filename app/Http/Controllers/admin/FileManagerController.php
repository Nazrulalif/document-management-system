<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\documentVersion;
use App\Models\Folder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;
use PhpOffice\PhpWord\IOFactory; // For Word documents
use PhpOffice\PhpWord\Element\TextRun;
use PhpOffice\PhpWord\Element\Text;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetIOFactory;

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
        $document = Document::join('folders', 'folders.id', '=', 'documents.folder_guid')
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
        // $folder = Folder::create($request->only('name', 'parent_id'));

        // return response()->json($folder, 201);

        $folder = new Folder();
        $folder->folder_name = $request->new_folder_name;
        $folder->parent_folder_guid = $request->new_folder_id;
        $folder->created_by = Auth::user()->id;
        $folder->org_guid = Auth::user()->org_guid;
        $folder->is_meeting = 'N';
        $folder->save();

        // dd($folder);

        // return response()->json(['success' => true, 'message' => 'Folder created successfully!']);
        return redirect()->back();
    }

    public function destroy($id)
    {

        $folder = Folder::find($id);

        if ($folder) {
            $folder->delete();

            return response()->json(['success' => true, 'message' => 'Folder deleted successfully']);
        }

        return response()->json(['success' => false, 'message' => 'Folder not found']);
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
        $ids = $request->input('ids');
        Folder::whereIn('id', $ids)->delete();

        return response()->json(['success' => true, 'message' => 'Folders deleted successfully']);
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

            // Delete the associated file if it exists in storage
            if (Storage::exists($filePath)) {
                Storage::delete($filePath); // Delete the file using Laravel's Storage facade
            }

            // Find the document in the documents table and delete it
            Document::where('id', $document->doc_guid)->delete();
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
            $filePath = $document->file_path;

            // Delete the associated file if it exists
            if (Storage::exists($filePath)) {
                Storage::delete($filePath);  // Delete file using Laravel's Storage facade
            }

            // Delete the document record from the database
            $documentId->delete();

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
            $originalFileName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();

            // Define folder based on file extension
            $folder = $this->getFolderByFileType($extension); // Separate function to get the folder

            // Store the file in the corresponding folder in 'storage/app/uploads/{folder}'
            $filePath = $file->storeAs('uploads/' . $folder, $originalFileName);

            $folder_id = $request->folder_id;

            // Initialize the document content variable
            $docContent = null;

            // If the file is a PDF, extract its content
            if ($extension === 'pdf') {
                $pdfParser = new Parser();
                $pdf = $pdfParser->parseFile(storage_path('app/' . $filePath));
                $docContent = $pdf->getText(); // Extract the text from the PDF
            }

            // If the file is a DOCX, extract its content using the method defined below
            if ($extension === 'docx') {
                $docContent = $this->extractTextFromDocx(storage_path('app/' . $filePath)); // Extract text from DOCX
            }


            // Handle Excel file
            if (in_array($extension, ['xlsx', 'xls', 'csv'])) {
                $docContent = $this->extractTextFromExcel(storage_path('app/' . $filePath)); // Extract text from Excel
            }

            // Check if OCR content was sent for images
            if (in_array($extension, ['jpeg', 'jpg', 'png']) && $request->has('ocr_content')) {
                $docContent = $request->input('ocr_content'); // Use the extracted OCR content from the request
            }

            // Store file information in the database
            $newFile = Document::create([
                'doc_name' => $originalFileName,
                'folder_guid' => $folder_id,
                'doc_type' => $folder,
                'upload_by' => Auth::user()->id,
                'org_guid' => Auth::user()->org_guid, // Assuming you're storing the org_guid too
            ]);

            documentVersion::create([
                'doc_guid' => $newFile->id,
                'file_path' => $filePath,
                'doc_content' => $docContent, // Store the extracted document content
                'created_by' => Auth::user()->id,
            ]);

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
