<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;

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
        ]);

        // Handle light theme logo upload
        if ($request->hasFile('nav_light_file')) {
            // Retrieve the old logo path from the database
            $oldLightLogo = SystemSetting::where('name', 'nav_light_logo')->first();

            // If there's an old logo, delete it from storage
            if ($oldLightLogo && $oldLightLogo->attribute) {
                $oldLightLogoPath = public_path('storage/' . $oldLightLogo->attribute);
                if (file_exists($oldLightLogoPath)) {
                    unlink($oldLightLogoPath); // Delete the old file
                }
            }

            // Handle the new upload
            $lightLogoPath = $request->file('nav_light_file');
            $extension = $lightLogoPath->getClientOriginalExtension();
            $uniqueFileName = time() . '_' . uniqid() . '.' . $extension;

            // Store the file in the 'uploads/system-logo' directory
            $lightLogoPath->storeAs('uploads/system-logo', $uniqueFileName, 'public');

            // Update or create the logo in the database
            SystemSetting::updateOrCreate(
                ['name' => 'nav_light_logo'],
                ['attribute' => 'uploads/system-logo/' . $uniqueFileName]
            );
        }

        // Handle dark theme logo upload
        if ($request->hasFile('nav_dark_file')) {
            // Retrieve the old logo path from the database
            $oldDarkLogo = SystemSetting::where('name', 'nav_dark_logo')->first();

            // If there's an old logo, delete it from storage
            if ($oldDarkLogo && $oldDarkLogo->attribute) {
                $oldDarkLogoPath = public_path('storage/' . $oldDarkLogo->attribute);
                if (file_exists($oldDarkLogoPath)) {
                    unlink($oldDarkLogoPath); // Delete the old file
                }
            }

            // Handle the new upload
            $darkLogoPath = $request->file('nav_dark_file');
            $extension = $darkLogoPath->getClientOriginalExtension();
            $uniqueFileName = time() . '_' . uniqid() . '.' . $extension;

            // Store the file in the 'uploads/system-logo' directory
            $darkLogoPath->storeAs('uploads/system-logo', $uniqueFileName, 'public');

            // Update or create the logo in the database
            SystemSetting::updateOrCreate(
                ['name' => 'nav_dark_logo'],
                ['attribute' => 'uploads/system-logo/' . $uniqueFileName]
            );
        }

        // Handle favicon logo upload
        if ($request->hasFile('favicon_file')) {
            // Retrieve the old logo path from the database
            $oldFavicon = SystemSetting::where('name', 'favicon')->first();

            // If there's an old logo, delete it from storage
            if ($oldFavicon && $oldFavicon->attribute) {
                $oldFaviconPath = public_path('storage/' . $oldFavicon->attribute);
                if (file_exists($oldFaviconPath)) {
                    unlink($oldFaviconPath); // Delete the old file
                }
            }

            // Handle the new upload
            $faviconPath = $request->file('favicon_file');
            $extension = $faviconPath->getClientOriginalExtension();
            $uniqueFileName = time() . '_' . uniqid() . '.' . $extension;

            // Store the file in the 'uploads/system-logo' directory
            $faviconPath->storeAs('uploads/system-logo', $uniqueFileName, 'public');

            // Update or create the logo in the database
            SystemSetting::updateOrCreate(
                ['name' => 'favicon'],
                ['attribute' => 'uploads/system-logo/' . $uniqueFileName]
            );
        }

        // Handle login background upload
        if ($request->hasFile('login_background_file')) {
            // Retrieve the old image path from the database
            $oldBackground = SystemSetting::where('name', 'login_background')->first();

            // If there's an old logo, delete it from storage
            if ($oldBackground && $oldBackground->attribute) {
                $oldBackgroundPath = public_path('storage/' . $oldBackground->attribute);
                if (file_exists($oldBackgroundPath)) {
                    unlink($oldBackgroundPath); // Delete the old file
                }
            }

            // Handle the new upload
            $BackgroundPath = $request->file('login_background_file');
            $extension = $BackgroundPath->getClientOriginalExtension();
            $uniqueFileName = time() . '_' . uniqid() . '.' . $extension;

            // Store the file in the 'uploads/system-logo' directory
            $BackgroundPath->storeAs('uploads/system-logo', $uniqueFileName, 'public');

            // Update or create the logo in the database
            SystemSetting::updateOrCreate(
                ['name' => 'login_background'],
                ['attribute' => 'uploads/system-logo/' . $uniqueFileName]
            );
        }

        if ($request->hasFile('login_logo_file')) {
            // Retrieve the old image path from the database
            $oldLoginLogo = SystemSetting::where('name', 'login_logo')->first();

            // If there's an old logo, delete it from storage
            if ($oldLoginLogo && $oldLoginLogo->attribute) {
                $oldLoginLogoPath = public_path('storage/' . $oldLoginLogo->attribute);
                if (file_exists($oldLoginLogoPath)) {
                    unlink($oldLoginLogoPath); // Delete the old file
                }
            }

            // Handle the new upload
            $LoginLogoPath = $request->file('login_logo_file');
            $extension = $LoginLogoPath->getClientOriginalExtension();
            $uniqueFileName = time() . '_' . uniqid() . '.' . $extension;

            // Store the file in the 'uploads/system-logo' directory
            $LoginLogoPath->storeAs('uploads/system-logo', $uniqueFileName, 'public');

            // Update or create the logo in the database
            SystemSetting::updateOrCreate(
                ['name' => 'login_logo'],
                ['attribute' => 'uploads/system-logo/' . $uniqueFileName]
            );
        }


        if ($request->logo_caption) {
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

        if ($pageLoaderValue) {
            SystemSetting::updateOrCreate(
                ['name' => 'page_loader'],
                ['attribute' => $pageLoaderValue]
            );
        }


        return redirect()->back()->with('success', 'System setting updated successfully!');
    }
}
