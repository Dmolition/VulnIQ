<?php

use Illuminate\Support\Facades\Route;
use App\Services\NessusService;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;
use App\Jobs\StartFullScanJob;


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




route::get('/',[AdminController::class,'home']);

Route::get('/our-services', function () {
    return view('home.our-services');
});




route::get('/home',[AdminController::class,'index'])->name('home');



route::get('/create_scan',[AdminController::class,'create_scan']);

route::get('/add_scan',[AdminController::class,'add_scan']);

route::get('/display_scan',[AdminController::class,'display_scan']);

route::get('/scan_delete/{id}',[AdminController::class,'scan_delete']);

route::get('/scan_start/{id}',[AdminController::class,'scan_start']);

route::get('/scan_detail',[AdminController::class,'scan_detail']);

<<<<<<< HEAD
route::get('/scans/{id}/download', [App\Http\Controllers\ScanController::class, 'download']);
=======
route::get('/brute_force', [AdminController::class, 'brute_force']);

route::get('/dictionary_attack', [AdminController::class, 'dictionary_attack']);

route::get('/sql_injection', [AdminController::class, 'sql_injection']);

route::get('/xss', [AdminController::class, 'xss']);

Route::post('/simulate_xss', [AdminController::class, 'simulate_xss']);

Route::get('/antivirus_game', function () {
    return view('home.antivirus_game');
});


Route::get('/phishing_game', function () {
    return view('home.phishing_game');
});

Route::get('/social_game', function () {
    return view('home.social_game');
});

Route::post('/simulatesqlmap', [AdminController::class, 'simulatesqlmap']);

Route::get('/admin/shell', [AdminController::class, 'adminShell'])->name('admin.shell');
Route::post('/admin/execute-shell-command', [AdminController::class, 'executeShellCommand'])->name('admin.execute-shell-command');
>>>>>>> 8bc5691f7be72658932517c35b406e2353e16eb9
