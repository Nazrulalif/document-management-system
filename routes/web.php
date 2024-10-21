<?php

use App\Http\Controllers\admin\CompanyController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\admin\FileController;
use App\Http\Controllers\admin\FileManagerController;
use App\Http\Controllers\admin\MyProfilController;
use App\Http\Controllers\admin\ReportController;
use App\Http\Controllers\admin\RoleController;
use App\Http\Controllers\admin\SearchController;
use App\Http\Controllers\admin\StarredController as AdminStarredController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\mail\UserRegisteredController;
use App\Http\Controllers\user\FileController as UserFileController;
use App\Http\Controllers\user\FileManagerController as UserFileManagerController;
use App\Http\Controllers\user\HomeController;
use App\Http\Controllers\user\MyProfilController as UserMyProfilController;
use App\Http\Controllers\user\SearchController as UserSearchController;
use App\Http\Controllers\user\StarredController;
use App\Models\AuditLog;
use App\Models\Organization;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|---------------------------------------------------------------------------
|                  2024
|                 Credits
|---------------------------------------------------------------------------
| Developed by Muhammad Nazrul Alif
| GitHub: https://github.com/Nazrulalif/
| WhatsApp: 014-9209024
|
| Feel free to explore and contribute to this project!
|---------------------------------------------------------------------------
*/

//Landing page
Route::get('/', function () {
    if (Auth::check()) {
        return redirect(route('dashboard.admin'));
    }
    $isParentExist = Organization::where('is_parent', 'Y')->first();

    return view('session.login-form', compact('isParentExist'));
})->name('login');
// Log out
Route::get('/logout', function (Request $request) {
    AuditLog::create([
        'action' => 'Logout',
        'model' => 'User',
        'user_guid' => Auth::user()->id,
        'ip_address' => $request->ip(),
    ]);

    // Invalidate the session (destroy all session data)
    $request->session()->invalidate();

    // Regenerate session token
    $request->session()->regenerateToken();

    // Redirect to login page
    return redirect(route('login'));
})->name('logout');
//Log in
Route::get('/login', [AuthController::class, 'index'])->name('login');
Route::post('/login-post', [AuthController::class, 'post'])->name('login.post');;
// forget password
Route::get('/forgot-password', [ForgotPasswordController::class, 'index'])->name('password.request');
Route::post('/email-verify', [ForgotPasswordController::class, 'email_verify'])->name('email.verify');
Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'reset_password'])->name('reset.password');
Route::post('/new-password', [ForgotPasswordController::class, 'reset_password_post'])->name('reset.password.post');

//register parent company
Route::get('/register-main', [AuthController::class, 'register_parent'])->name('register.parent');
Route::post('/register-main-post', [AuthController::class, 'register_parent_post'])->name('register.parent.post');
Route::get('/register-success', [AuthController::class, 'register_success'])->name('register.parent.success');

// Admin Routes
route::prefix('admin')->middleware('isadmin')->group(function () {

    // Dasboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard.admin');

    // Apply role middleware to restrict access based on role_guid = 1
    Route::middleware('role:1')->group(function () {
        // Company List
        Route::get('/company-list', [CompanyController::class, 'index'])->name('company.index');
        Route::get('/company-detail/{uuid}', [CompanyController::class, 'view'])->name('company.view');
        Route::get('/company-file/{uuid}', [CompanyController::class, 'file'])->name('company.file');
        Route::get('/company-setting/{uuid}', [CompanyController::class, 'setting'])->name('company.setting');
        Route::post('/company-setting-post/{uuid}', [CompanyController::class, 'setting_post'])->name('company.settingPost');
        Route::post('/company-deactivate/{uuid}', [CompanyController::class, 'deactivate'])->name('company.deactivate');
        Route::post('/company-destroy/{id}', [CompanyController::class, 'destroy'])->name('company.destroy');
        Route::post('/company-bulk-destroy', [CompanyController::class, 'bulk_destroy'])->name('company.bulk.destroy');
        Route::post('/create-company', [CompanyController::class, 'create'])->name('company.create');
        Route::post('/company-update/{id}', [CompanyController::class, 'update'])->name('company.update');
        Route::get('/company-show/{id}', [CompanyController::class, 'show'])->name('company.show');

        //role list
        Route::get('/role-list', [RoleController::class, 'index'])->name('role.index');
        Route::get('/role-show/{id}', [RoleController::class, 'show'])->name('company.show');
        Route::post('/role-update/{id}', [RoleController::class, 'update'])->name('role.update');
        //view role
        Route::get('/view-role/{uuid}', [RoleController::class, 'view'])->name('role.view');
    });

    Route::middleware('role:1,2')->group(function () {
        //User list
        Route::get('/user-list', [UserController::class, 'index'])->name('user.index');
        //add User
        Route::post('/create-user', [UserController::class, 'create'])->name('user.create');
        Route::post('/import-user', [UserController::class, 'import'])->name('user.import');
        //Deactive User
        Route::post('/user-deactive/{id}', [UserController::class, 'deactive'])->name('user.deactive');
        //bulk deactive user
        route::get('/download-template', [UserController::class, 'downloadTemplate'])->name('download.template');
        Route::post('/user-bulk-deactive', [UserController::class, 'bulk_deactive'])->name('user.bulk.deactive');
        //user update
        Route::get('/user-show/{id}', [UserController::class, 'show'])->name('user.show');
        Route::post('/user-update/{id}', [UserController::class, 'update'])->name('user.update');
        //view user
        Route::get('/user-detail/{uuid}', [UserController::class, 'view'])->name('user.view');
        Route::get('/user-file/{uuid}', [UserController::class, 'file'])->name('user.file');
        Route::get('/user-setting/{uuid}', [UserController::class, 'setting'])->name('user.setting');
        route::post('/user-setting-post/{id}', [UserController::class, 'user_setting_post'])->name('userSetting.post');
        Route::post('/user-setting-deactivate/{uuid}', [UserController::class, 'setting_deactive'])->name('userSetting.deactive');
    });

    //Starred post
    Route::post('/star', [FileManagerController::class, 'star'])->name('star.user');

    //Starred
    Route::get('/starred', [AdminStarredController::class, 'index'])->name('starred.index');

    //user account registered email
    Route::get('/user-registered-mail', [UserRegisteredController::class, 'user_registered'])->name('email.registered');

    //File Manager
    Route::get('/file-manager', [FileManagerController::class, 'index'])->name('fileManager.index');
    route::get('/file-manager/fetch-data', [FileManagerController::class, 'fetchData'])->name('fileManager.fetchData');
    route::get('/file-manager/{uuid}', [FileManagerController::class, 'show_folder'])->name('folder.show');
    //create folder
    Route::post('/create-folder', [FileManagerController::class, 'create'])->name('folder.create');
    // rename folder
    Route::post('/folder-rename/{id}', [FileManagerController::class, 'rename'])->name('folder.rename');
    //delete folder by row
    Route::post('/folder-destroy/{id}', [FileManagerController::class, 'destroy'])->name('folder.destroy');
    // delete folder by selected rows
    route::post('/folder/delete-selected', [FileManagerController::class, 'deleteSelected']);
    // upload file
    route::post('/file-upload', [FileManagerController::class, 'upload_file'])->name('file.upload');
    // delete file
    route::post('/file-destroy/{id}', [FileManagerController::class, 'destroy_file'])->name('file.destroy');
    //delete file
    route::post('/file/delete-selected', [FileManagerController::class, 'file_deleteSelected']);

    //view file page
    route::get('/file-details/{uuid}', [FileController::class, 'index'])->name('file.index');
    //update file detail
    route::post('/file-update/{uuid}', [FileController::class, 'update'])->name('file.update');
    //generate summary with AI
    route::get('/generate-summary/{uuid}', [FileController::class, 'generate_summary'])->name('generate.summary');
    //Add new version
    Route::post('/add-version', [FileController::class, 'add_version'])->name('file.add.version');
    //destroy file
    route::post('/file-detail-destroy/{uuid}', [FileController::class, 'destroy_file'])->name('file.detail.destroy');

    //advance search
    route::get('/advance-search', [SearchController::class, 'index'])->name('search.index');

    //report
    route::get('/report', [ReportController::class, 'index'])->name('report.index');
    //gerate report
    route::post('/generated-report', [ReportController::class, 'post'])->name('report.post');

    //my profile
    route::get('/my-profile', [MyProfilController::class, 'index'])->name('profile.index');
    route::get('/my-file', [MyProfilController::class, 'file'])->name('profile.file');
    route::get('/setting', [MyProfilController::class, 'setting'])->name('profile.setting');
    route::post('/setting-post', [MyProfilController::class, 'setting_post'])->name('setting.post');
    route::post('/change-password', [MyProfilController::class, 'change_password'])->name('password.change');
});

// User Routes
Route::middleware(['auth', 'web', 'isuser'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home.user');

    //file manager
    Route::get('/file-manager', [UserFileManagerController::class, 'index'])->name('file-manager.user');
    route::get('/file-manager/{uuid}', [UserFileManagerController::class, 'show_folder'])->name('folder.show.user');
    Route::post('/star', [UserFileManagerController::class, 'star'])->name('star.user');

    // file detail
    route::get('/file-details/{uuid}', [UserFileController::class, 'index'])->name('file.user');


    //starred
    Route::get('/starred', [StarredController::class, 'index'])->name('starred.user');

    //advance Search
    Route::get('/advance-search', [UserSearchController::class, 'index'])->name('advance-search.user');

    //my profile
    Route::get('/my-profile', [UserMyProfilController::class, 'index'])->name('profile.user');
    Route::get('/setting', [UserMyProfilController::class, 'setting'])->name('profile.setting.user');
    Route::post('/setting-post', [UserMyProfilController::class, 'setting_post'])->name('setting.user.post');
    Route::post('/password-change', [UserMyProfilController::class, 'change_password'])->name('password.change.user');
});
