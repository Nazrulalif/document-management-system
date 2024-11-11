<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        // Retrieve logo paths from the database
        $lightLogo = SystemSetting::where('name', 'nav_light_logo')->first();
        $darkLogo = SystemSetting::where('name', 'nav_dark_logo')->first();
        $favicon = SystemSetting::where('name', 'favicon')->first();
        $systemName = SystemSetting::where('name', 'system_name')->first();
        $pageLoader = SystemSetting::where('name', 'page_loader')->first();
        $loginBackground = SystemSetting::where('name', 'login_background')->first();
        $loginLogo = SystemSetting::where('name', 'login_logo')->first();
        $logoCaption = SystemSetting::where('name', 'logo_caption')->first();

        return view('admin.setting.main', compact(
            'lightLogo',
            'darkLogo',
            'favicon',
            'systemName',
            'pageLoader',
            'loginBackground',
            'loginLogo',
            'logoCaption',
        ));
    }

    public function main_post(Request $request)
    {
        // Validate the request
        $request->validate([
            'nav_light_file' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'nav_dark_file' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'favicon_file' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'login_background_file' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
            'login_logo_file' => 'nullable|image|mimes:png,jpg,jpeg,svg|max:2048',
        ]);

        // Handle different file uploads
        $this->handleFileUpload($request, 'nav_light_file', 'nav_light_logo');
        $this->handleFileUpload($request, 'nav_dark_file', 'nav_dark_logo');
        $this->handleFileUpload($request, 'favicon_file', 'favicon');
        $this->handleFileUpload($request, 'login_background_file', 'login_background');
        $this->handleFileUpload($request, 'login_logo_file', 'login_logo');

        // Update text-based settings
        if ($request->system_name) {
            SystemSetting::updateOrCreate(
                ['name' => 'system_name'],
                ['attribute' => $request->system_name]
            );
        }

        if ($request->logo_caption) {
            SystemSetting::updateOrCreate(
                ['name' => 'logo_caption'],
                ['attribute' => $request->logo_caption]
            );
        }

        $pageLoaderValue = $request->has('page_loader') ? 'Y' : 'N';
        SystemSetting::updateOrCreate(
            ['name' => 'page_loader'],
            ['attribute' => $pageLoaderValue]
        );

        return redirect()->back()->with('success', 'System setting updated successfully!');
    }

    /**
     * Handle file upload for a given setting.
     */
    private function handleFileUpload(Request $request, $fileInput, $settingName)
    {
        if ($request->hasFile($fileInput)) {
            // Retrieve the old file path from the database
            $oldSetting = SystemSetting::where('name', $settingName)->first();

            // If there's an old file, delete it from SFTP storage
            if ($oldSetting && $oldSetting->attribute) {
                if (Storage::exists($oldSetting->attribute)) {
                    Storage::delete($oldSetting->attribute);
                }
            }

            // Handle new file upload
            $file = $request->file($fileInput);
            $uniqueFileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $filePath = 'uploads/system-logo/' . $uniqueFileName;

            // Store the file on SFTP disk
            Storage::put($filePath, file_get_contents($file));

            // Update or create the setting in the database
            SystemSetting::updateOrCreate(
                ['name' => $settingName],
                ['attribute' => $filePath]
            );
        }
    }
}
