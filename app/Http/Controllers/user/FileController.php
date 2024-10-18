<?php

namespace App\Http\Controllers\user;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Document;
use App\Models\documentVersion;
use Illuminate\Http\Request;

class FileController extends Controller
{
    public function index($uuid)
    {
        $file = Document::select('*', 'documents.created_at as created_at', 'document_versions.uuid as uuid')
            ->join('document_versions', 'document_versions.uuid', '=', 'documents.latest_version_guid')
            ->join('users', 'users.id', '=', 'documents.upload_by')
            ->join('organizations', 'organizations.id', '=', 'documents.org_guid')
            ->where('document_versions.uuid', '=', $uuid)
            ->first();

        $version = documentVersion::select('*', 'document_versions.uuid as uuid', 'document_versions.created_at as created_at')
            ->join('documents', 'documents.id', '=', 'document_versions.doc_guid')
            ->join('users', 'users.id', '=', 'document_versions.created_by')
            ->where('documents.latest_version_guid', '=', $uuid)
            ->orderBy('document_versions.created_at', 'desc')
            ->get();

        if (!$file) {
            return abort(404, 'Document not found'); // Or you could redirect to another page with an error message
        }

        $audit_logs = AuditLog::select('*', 'audit_logs.created_at as created_at')
            ->join('documents', 'documents.id', '=', 'audit_logs.doc_guid')
            ->join('users', 'users.id', '=', 'documents.upload_by')
            ->where('documents.latest_version_guid', $uuid)
            ->orderBy('audit_logs.created_at', 'desc')
            ->get();
        // Split the doc_summary into lines (if applicable)
        $doc_summary = explode("\n", $file->doc_summary);
        return view('user.file-manager.detail-page', [
            'data' => $file,
            'doc_summary' => $doc_summary,
            'version' => $version,
            'audit_logs' => $audit_logs,

        ]);
    }
}
