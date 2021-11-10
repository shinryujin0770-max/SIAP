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


// SITU
Route::get('/situ', [App\Http\Controllers\SituController::class, 'index'])->name('situ');
Route::get('/situ/browse', [App\Http\Controllers\SituController::class, 'browse'])->name('situ.browse');
Route::get('/situ/add', [App\Http\Controllers\SituController::class, 'add'])->name('situ.add');
Route::get('/situ/{id}', [App\Http\Controllers\SituController::class, 'read'])->name('situ.read');
Route::get('/situ/{id}/certificate', [App\Http\Controllers\SituController::class, 'previewCertificates'])->name('situ.certificate');
Route::get('/situ/{id}/skrd', [App\Http\Controllers\SituController::class, 'previewSKRD'])->name('situ.skrd');
Route::get('/situ/edit/{id}', [App\Http\Controllers\SituController::class, 'edit'])->name('situ.edit');
Route::post('/situ/store', [App\Http\Controllers\SituController::class, 'store'])->name('situ.store');
Route::post('/situ/update', [App\Http\Controllers\SituController::class, 'update'])->name('situ.update');
Route::get('/situ/modalDelete/{id}', [App\Http\Controllers\SituController::class, 'modalDelete'])->name('situ.modalDelete');
Route::post('/situ/delete/{id}', [App\Http\Controllers\SituController::class, 'delete'])->name('situ.delete');
Route::get('/situ/modalSignature/{id}', [App\Http\Controllers\SituController::class, 'modalSignature'])->name('situ.modalSignature');
Route::post('/situ/signature/{id}', [App\Http\Controllers\SituController::class, 'signature'])->name('situ.signature');
Route::get('/situ_reregistration', [App\Http\Controllers\SituReregistrationController::class, 'index'])->name('situ.reregistration');
Route::get('/situ_reregistration/browse', [App\Http\Controllers\SituReregistrationController::class, 'browse'])->name('situ.reregistration.browse');
Route::get('/situ_reregistration/modalRequest/{id}', [App\Http\Controllers\SituReregistrationController::class, 'modalRequestReregistration'])->name('situ.reregistration.modalRequestReregistration');
Route::post('/situ_reregistration/requestReregistration/{id}', [App\Http\Controllers\SituReregistrationController::class, 'requestReregistration'])->name('situ.reregistration.requestReregistration');
Route::get('/situ_reregistration/modalSignature/{id}', [App\Http\Controllers\SituReregistrationController::class, 'modalSignature'])->name('situ.reregistration.modalSignature');
Route::post('/situ_reregistration/signature/{id}', [App\Http\Controllers\SituReregistrationController::class, 'signature'])->name('situ.reregistration.signature');
Route::get('/situ_reregistration/modalDelete/{id}', [App\Http\Controllers\SituReregistrationController::class, 'modalDelete'])->name('situ.reregistration.modalDelete');
Route::post('/situ_reregistration/delete/{id}', [App\Http\Controllers\SituReregistrationController::class, 'delete'])->name('situ.reregistration.delete');

// Jenis Dagangan
Route::get('/jenis_dagangan', [App\Http\Controllers\JenisDaganganController::class, 'index'])->name('jenis_dagangan');
Route::get('/jenis_dagangan/browse', [App\Http\Controllers\JenisDaganganController::class, 'browse'])->name('jenis_dagangan.browse');
Route::get('/jenis_dagangan/add', [App\Http\Controllers\JenisDaganganController::class, 'add'])->name('jenis_dagangan.add');
Route::get('/jenis_dagangan/edit/{id}', [App\Http\Controllers\JenisDaganganController::class, 'edit'])->name('jenis_dagangan.edit');
Route::post('/jenis_dagangan/store', [App\Http\Controllers\JenisDaganganController::class, 'store'])->name('jenis_dagangan.store');
Route::post('/jenis_dagangan/update', [App\Http\Controllers\JenisDaganganController::class, 'update'])->name('jenis_dagangan.update');
Route::get('/jenis_dagangan/modalDelete/{id}', [App\Http\Controllers\JenisDaganganController::class, 'modalDelete'])->name('jenis_dagangan.modalDelete');
Route::post('/jenis_dagangan/delete/{id}', [App\Http\Controllers\JenisDaganganController::class, 'delete'])->name('jenis_dagangan.delete');

// Kios
Route::get('/kios', [App\Http\Controllers\KiosController::class, 'index'])->name('kios');
Route::get('/kios/browse', [App\Http\Controllers\KiosController::class, 'browse'])->name('kios.browse');
Route::get('/kios/add', [App\Http\Controllers\KiosController::class, 'add'])->name('kios.add');
Route::get('/kios/edit/{id}', [App\Http\Controllers\KiosController::class, 'edit'])->name('kios.edit');
Route::post('/kios/store', [App\Http\Controllers\KiosController::class, 'store'])->name('kios.store');
Route::post('/kios/update', [App\Http\Controllers\KiosController::class, 'update'])->name('kios.update');
Route::get('/kios/modalDelete/{id}', [App\Http\Controllers\KiosController::class, 'modalDelete'])->name('kios.modalDelete');
Route::post('/kios/delete/{id}', [App\Http\Controllers\KiosController::class, 'delete'])->name('kios.delete');
Route::get('/kios/ajax/{id}', [App\Http\Controllers\KiosController::class, 'ajax'])->name('kios.ajax');

// Pasar
Route::get('/pasar', [App\Http\Controllers\PasarController::class, 'index'])->name('pasar');
Route::get('/pasar/browse', [App\Http\Controllers\PasarController::class, 'browse'])->name('pasar.browse');
Route::get('/pasar/add', [App\Http\Controllers\PasarController::class, 'add'])->name('pasar.add');
Route::get('/pasar/edit/{id}', [App\Http\Controllers\PasarController::class, 'edit'])->name('pasar.edit');
Route::post('/pasar/store', [App\Http\Controllers\PasarController::class, 'store'])->name('pasar.store');
Route::post('/pasar/update', [App\Http\Controllers\PasarController::class, 'update'])->name('pasar.update');
Route::get('/pasar/modalDelete/{id}', [App\Http\Controllers\PasarController::class, 'modalDelete'])->name('pasar.modalDelete');
Route::post('/pasar/delete/{id}', [App\Http\Controllers\PasarController::class, 'delete'])->name('pasar.delete');

// Pedagang
Route::get('/pedagang', [App\Http\Controllers\PedagangController::class, 'index'])->name('pedagang');
Route::get('/pedagang/browse', [App\Http\Controllers\PedagangController::class, 'browse'])->name('pedagang.browse');
Route::get('/pedagang/edit/{id}', [App\Http\Controllers\PedagangController::class, 'edit'])->name('pedagang.edit');
Route::post('/pedagang/update', [App\Http\Controllers\PedagangController::class, 'update'])->name('pedagang.update');
Route::get('/pedagang/ajax/{id}', [App\Http\Controllers\PedagangController::class, 'ajax'])->name('pedagang.ajax');

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
