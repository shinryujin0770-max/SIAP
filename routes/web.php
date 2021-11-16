<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('login');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/dashboard', 'DashboardController@index')->name('dashboard');


// project
Route::group(['middleware' => ['permission:project-write']], function () {
    Route::get('/project/add', [App\Http\Controllers\ProjectController::class, 'add'])->name('project.add');
    Route::get('/project/edit/{id}', [App\Http\Controllers\ProjectController::class, 'edit'])->name('project.edit');
    Route::post('/project/store', [App\Http\Controllers\ProjectController::class, 'store'])->name('project.store');
    Route::post('/project/update', [App\Http\Controllers\ProjectController::class, 'update'])->name('project.update');
    Route::get('/project/modalDelete/{id}', [App\Http\Controllers\ProjectController::class, 'modalDelete'])->name('project.modalDelete');
    Route::post('/project/delete/{id}', [App\Http\Controllers\ProjectController::class, 'delete'])->name('project.delete');

    // partner
    Route::get('/partner', [App\Http\Controllers\PartnerController::class, 'index'])->name('partner');
    Route::get('/partner/browse', [App\Http\Controllers\PartnerController::class, 'browse'])->name('partner.browse');
});

Route::group(['middleware' => ['permission:project-read']], function () {
    Route::get('/project', [App\Http\Controllers\ProjectController::class, 'index'])->name('project');
    Route::get('/project/browse', [App\Http\Controllers\ProjectController::class, 'browse'])->name('project.browse');
    Route::get('/project/{id}', [App\Http\Controllers\ProjectController::class, 'read'])->name('project.read');

    Route::get('/project/payment/{id}', [App\Http\Controllers\ProjectPaymentController::class, 'detail'])->name('project.payment');
    Route::get('/project/payment/{id}/edit', [App\Http\Controllers\ProjectPaymentController::class, 'edit'])->name('project.payment.edit');
});

Route::group(['middleware' => ['permission:pengaturan-all']], function () {
    // Users
    Route::get('/user', [App\Http\Controllers\UserController::class, 'index'])->name('user');
    Route::get('/user/browse', [App\Http\Controllers\UserController::class, 'browse'])->name('user.browse');
    Route::get('/user/add', [App\Http\Controllers\UserController::class, 'add'])->name('user.add');
    Route::get('/user/{id}', [App\Http\Controllers\UserController::class, 'edit'])->name('user.edit');
    Route::post('/user/store', [App\Http\Controllers\UserController::class, 'store'])->name('user.store');
    Route::post('/user/update', [App\Http\Controllers\UserController::class, 'update'])->name('user.update');
    Route::get('/user/modalDelete/{id}', [App\Http\Controllers\UserController::class, 'modalDelete'])->name('user.modalDelete');
    Route::post('/user/delete/{id}', [App\Http\Controllers\UserController::class, 'delete'])->name('user.delete');
    Route::get('/user/ajax/{id}', [App\Http\Controllers\UserController::class, 'ajax'])->name('user.ajax');
});

// Ajax
Route::get('/partner/ajax/{id}', [App\Http\Controllers\PartnerController::class, 'ajax'])->name('partner.ajax');
