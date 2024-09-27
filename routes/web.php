<?php

use App\Http\Controllers\admin\CompanyController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\admin\FileController;
use App\Http\Controllers\admin\FileManagerController;
use App\Http\Controllers\admin\MyProfilController;
use App\Http\Controllers\admin\ReportController;
use App\Http\Controllers\admin\RoleController;
use App\Http\Controllers\admin\SearchController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\mail\UserRegisteredController;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//Landing page
Route::get('/', function () {
    return view('session.login');
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
// Admin Routes
route::prefix('admin')->middleware('isadmin')->group(function () {

    // Dasboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard.admin');

    // Company List
    Route::get('/company-list', [CompanyController::class, 'index'])->name('company.index');
    Route::get('/company-detail/{uuid}', [CompanyController::class, 'view'])->name('company.view');
    Route::get('/company-file/{uuid}', [CompanyController::class, 'file'])->name('company.file');
    Route::get('/company-setting/{uuid}', [CompanyController::class, 'setting'])->name('company.setting');
    Route::post('/company-setting-post/{uuid}', [CompanyController::class, 'setting_post'])->name('company.settingPost');
    Route::post('/company-deactivate/{uuid}', [CompanyController::class, 'deactivate'])->name('company.deactivate');
    //destroy company
    Route::post('/company-destroy/{id}', [CompanyController::class, 'destroy'])->name('company.destroy');
    //bulk destroy company
    Route::post('/company-bulk-destroy', [CompanyController::class, 'bulk_destroy'])->name('company.bulk.destroy');
    // add company
    Route::post('/create-company', [CompanyController::class, 'create'])->name('company.create');
    // Company update
    Route::post('/company-update/{id}', [CompanyController::class, 'update'])->name('company.update');
    Route::get('/company-show/{id}', [CompanyController::class, 'show'])->name('company.show');

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


    //user account registered email
    Route::get('/user-registered-mail', [UserRegisteredController::class, 'user_registered'])->name('email.registered');

    //role list
    Route::get('/role-list', [RoleController::class, 'index'])->name('role.index');
    Route::get('/role-show/{id}', [RoleController::class, 'show'])->name('company.show');
    Route::post('/role-update/{id}', [RoleController::class, 'update'])->name('role.update');
    //view role
    Route::get('/view-role/{uuid}', [RoleController::class, 'view'])->name('role.view');

    //file manager
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
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard.user');
});
