<?php

use App\Http\Controllers\admin\CompanyController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\admin\UserController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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
Route::get('/logout', function () {
    Auth::logout();
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
    //destroy company
    Route::delete('/company-destroy/{id}', [CompanyController::class, 'destroy'])->name('company.destroy');
    //bulk destroy company
    Route::delete('/company-bulk-destroy', [CompanyController::class, 'bulk_destroy'])->name('company.bulk.destroy');
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
    Route::post('/user-deactive/{id}', [UserController::class, 'deactive'])->name('user.deactive');
    //bulk deactive user
    Route::post('/user-bulk-deactive', [UserController::class, 'bulk_deactive'])->name('user.bulk.deactive');
    //user update
    Route::get('/user-show/{id}', [UserController::class, 'show'])->name('user.show');
    Route::post('/user-update/{id}', [UserController::class, 'update'])->name('user.update');

    //user account registered email
    Route::get('/registered', [UserController::class, 'user_registered'])->name('email.registered');
});

// User Routes
Route::middleware(['auth', 'web', 'isuser'])->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard.user');
});
