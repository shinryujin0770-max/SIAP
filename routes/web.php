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
    return view('welcome');
});

Auth::routes();

// Route::get('/home', 'HomeController@index')->name('home');
Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
Route::get('/dashboard/rfk-data', 'DashboardController@getRFKData')->name('dashboard.rfk-data');

// project
Route::group(['middleware' => ['permission:project-write']], function () {
    Route::get('/project/modalAdd', [App\Http\Controllers\ProjectController::class, 'modalAdd'])->name('project.modalAdd');
    Route::get('/project/add', [App\Http\Controllers\ProjectController::class, 'add'])->name('project.add');
    Route::get('/project/edit/{id}', [App\Http\Controllers\ProjectController::class, 'edit'])->name('project.edit');
    Route::post('/project/store', [App\Http\Controllers\ProjectController::class, 'store'])->name('project.store');
    Route::post('/project/update', [App\Http\Controllers\ProjectController::class, 'update'])->name('project.update');
    Route::get('/project/modalDelete/{id}', [App\Http\Controllers\ProjectController::class, 'modalDelete'])->name('project.modalDelete');
    Route::post('/project/delete/{id}', [App\Http\Controllers\ProjectController::class, 'delete'])->name('project.delete');
    Route::post('/project/deleteMany/{id}', [App\Http\Controllers\ProjectController::class, 'deleteMany'])->name('project.deleteMany');

    Route::get('/project/{id}/details/{rekening}', [App\Http\Controllers\ProjectDetailController::class, 'details'])->name('project.details');
    Route::get('/project/{id}/detail/{id_detail}', [App\Http\Controllers\ProjectDetailController::class, 'modalDetail'])->name('project.detail');
    Route::post('/project/{id}/detail/{id_detail}', [App\Http\Controllers\ProjectDetailController::class, 'edit'])->name('project.detail.edit');

    Route::get('/project/{id}/pengantar/{type}/{rekening}', [App\Http\Controllers\ProjectDetailController::class, 'modalPengantar'])->name('project.modalPengantar');
    Route::get('/project/{id}/checklist/{type}/{rekening}', [App\Http\Controllers\ProjectDetailController::class, 'modalChecklist'])->name('project.modalChecklist');
    Route::post('/project/{id}/checklist', [App\Http\Controllers\ProjectDetailController::class, 'generateChecklist'])->name('project.generateChecklist');

    Route::post('/project/{id}/overtime', [App\Http\Controllers\ProjectDetailController::class, 'overtime'])->name('project.overtime');

    // partner
    Route::get('/partner', [App\Http\Controllers\PartnerController::class, 'index'])->name('partner');
    Route::get('/partner/browse', [App\Http\Controllers\PartnerController::class, 'browse'])->name('partner.browse');
});

Route::group(['middleware' => ['permission:project-read']], function () {
    Route::get('/project', [App\Http\Controllers\ProjectController::class, 'index'])->name('project');
    Route::get('/project/browse', [App\Http\Controllers\ProjectController::class, 'browse'])->name('project.browse');
    Route::get('/project/{id}', [App\Http\Controllers\ProjectController::class, 'read'])->name('project.read');

    Route::get('/project/print/{id}', [App\Http\Controllers\ProjectDetailController::class, 'print'])->name('project.print');
    Route::get('/project/printChecklist/{id}/{type}', [App\Http\Controllers\ProjectDetailController::class, 'printChecklist'])->name('project.printChecklist');
});

Route::group(['middleware' => ['role:admin_rfk|superadmin']], function () {
    Route::get('/rfk', [App\Http\Controllers\RfkController::class, 'index'])->name('rfk');
    Route::get('/rfk/browse', [App\Http\Controllers\RfkController::class, 'browse'])->name('rfk.browse');
    Route::get('/rfk/modalAdd', [App\Http\Controllers\RfkController::class, 'modalAdd'])->name('rfk.modalAdd');
    Route::get('/rfk/modalEdit/{id}', [App\Http\Controllers\RfkController::class, 'modalEdit'])->name('rfk.modalEdit');
    Route::get('/rfk/modalDelete/{id}', [App\Http\Controllers\RfkController::class, 'modalDelete'])->name('rfk.modalDelete');
    Route::post('/rfk', [App\Http\Controllers\RfkController::class, 'store'])->name('rfk.store');
    Route::post('/rfk/edit/{id}', [App\Http\Controllers\RfkController::class, 'editDetail'])->name('rfk.editDetail');
    Route::post('/rfk/delete/{id}', [App\Http\Controllers\RfkController::class, 'deleteDetail'])->name('rfk.deleteDetail');
    Route::post('/rfk/save', [App\Http\Controllers\RfkController::class, 'save'])->name('rfk.save');

    Route::post('/rfk/superadmin', [App\Http\Controllers\RfkController::class, 'storeSuperadmin'])->name('rfk.superadmin.store');
    Route::post('/rfk/superadmin/save', [App\Http\Controllers\RfkController::class, 'approve'])->name('rfk.superadmin.save');

    Route::get('/rfk/laporan/{type}', [App\Http\Controllers\RfkController::class, 'export'])->name('rfk.export');
    Route::get('/rfk/chart', [App\Http\Controllers\RfkController::class, 'getChart'])->name('rfk.chart');
    Route::get('/rfk/modalImport', [App\Http\Controllers\RfkController::class, 'modalImport'])->name('rfk.modalImport');
    Route::post('/rfk/import', [App\Http\Controllers\RfkController::class, 'import'])->name('rfk.import');

    Route::get('/rfk-v2', [App\Http\Controllers\RfkV2Controller::class, 'index'])->name('rfk-v2');
    Route::get('/rfk-v2/browse', [App\Http\Controllers\RfkV2Controller::class, 'browse'])->name('rfk-v2.browse');
    Route::get('/rfk-v2/fetchChildren', [App\Http\Controllers\RfkV2Controller::class, 'fetchChildren'])->name('rfk-v2.fetchChildren');
    Route::get('/rfk-v2/opd-average', [App\Http\Controllers\RfkV2Controller::class, 'getOpdAverage'])->name('rfk-v2.opdAverage');
    Route::get('/rfk-v2/modalAdd', [App\Http\Controllers\RfkV2Controller::class, 'modalAdd'])->name('rfk-v2.modalAdd');
    Route::get('/rfk-v2/modalAddChild/{parent_id}', [App\Http\Controllers\RfkV2Controller::class, 'modalAddChild'])->name('rfk-v2.modalAddChild');
    Route::get('/rfk-v2/modalEdit/{id}', [App\Http\Controllers\RfkV2Controller::class, 'modalEdit'])->name('rfk-v2.modalEdit');
    Route::get('/rfk-v2/modalDelete/{id}', [App\Http\Controllers\RfkV2Controller::class, 'modalDelete'])->name('rfk-v2.modalDelete');
    Route::post('/rfk-v2', [App\Http\Controllers\RfkV2Controller::class, 'store'])->name('rfk-v2.store');
    Route::post('/rfk-v2/edit/{id}', [App\Http\Controllers\RfkV2Controller::class, 'editDetail'])->name('rfk-v2.editDetail');
    Route::post('/rfk-v2/delete/{id}', [App\Http\Controllers\RfkV2Controller::class, 'deleteDetail'])->name('rfk-v2.deleteDetail');
    Route::post('/rfk-v2/cascadeDelete/{id}', [App\Http\Controllers\RfkV2Controller::class, 'cascadeDelete'])->name('rfk-v2.cascadeDelete');
    Route::post('/rfk-v2/ensureMonthData', [App\Http\Controllers\RfkV2Controller::class, 'ensureMonthData'])->name('rfk-v2.ensureMonthData');
    Route::post('/rfk-v2/resync', [App\Http\Controllers\RfkV2Controller::class, 'resyncFromPrevious'])->name('rfk-v2.resync');
    Route::post('/rfk-v2/toggle-overtime', [App\Http\Controllers\RfkV2Controller::class, 'toggleOvertime'])->name('rfk-v2.toggleOvertime');
    Route::post('/rfk-v2/save', [App\Http\Controllers\RfkV2Controller::class, 'save'])->name('rfk-v2.save');

    Route::post('/rfk-v2/superadmin', [App\Http\Controllers\RfkV2Controller::class, 'storeSuperadmin'])->name('rfk-v2.superadmin.store');
    Route::post('/rfk-v2/superadmin/save', [App\Http\Controllers\RfkV2Controller::class, 'approve'])->name('rfk-v2.superadmin.save');

    Route::get('/rfk-v2/laporan/{type}', [App\Http\Controllers\RfkV2Controller::class, 'export'])->name('rfk-v2.export');
    Route::get('/rfk-v2/chart', [App\Http\Controllers\RfkV2Controller::class, 'getChart'])->name('rfk-v2.chart');
    Route::get('/rfk-v2/modalImport', [App\Http\Controllers\RfkV2Controller::class, 'modalImport'])->name('rfk-v2.modalImport');
    Route::post('/rfk-v2/import', [App\Http\Controllers\RfkV2Controller::class, 'import'])->name('rfk-v2.import');
});

Route::group(['middleware' => ['permission:pengaturan-all']], function () {
    Route::get('/payment', [App\Http\Controllers\PaymentController::class, 'index'])->name('payment');
    Route::get('/payment/browse', [App\Http\Controllers\PaymentController::class, 'browse'])->name('payment.browse');

    Route::get('/report', [App\Http\Controllers\ReportController::class, 'index'])->name('report');
    Route::get('/report/browse', [App\Http\Controllers\ReportController::class, 'browse'])->name('report.browse');
    Route::get('/report/export', [App\Http\Controllers\ReportController::class, 'export'])->name('report.export');

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

    Route::get('/opd', [App\Http\Controllers\OpdController::class, 'index'])->name('opd');
    Route::get('/opd/browse', [App\Http\Controllers\OpdController::class, 'browse'])->name('opd.browse');
    Route::get('/opd/add', [App\Http\Controllers\OpdController::class, 'add'])->name('opd.add');
    Route::get('/opd/{id}', [App\Http\Controllers\OpdController::class, 'edit'])->name('opd.edit');
    Route::post('/opd/store', [App\Http\Controllers\OpdController::class, 'store'])->name('opd.store');
    Route::post('/opd/update', [App\Http\Controllers\OpdController::class, 'update'])->name('opd.update');
    Route::get('/opd/modalDelete/{id}', [App\Http\Controllers\OpdController::class, 'modalDelete'])->name('opd.modalDelete');
    Route::post('/opd/delete/{id}', [App\Http\Controllers\OpdController::class, 'delete'])->name('opd.delete');
});

// Ajax
Route::get('/partner/ajax/{id}', [App\Http\Controllers\PartnerController::class, 'ajax'])->name('partner.ajax');
Route::get('/ajax/kelurahan/{id_kecamatan}', [App\Http\Controllers\AjaxController::class, 'kelurahan'])->name('ajax.kelurahan');
