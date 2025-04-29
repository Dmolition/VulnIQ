<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;

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




route::get('/home',[AdminController::class,'index'])->name('home');



route::get('/create_scan',[AdminController::class,'create_scan']);

route::get('/add_scan',[AdminController::class,'add_scan']);

route::get('/display_scan',[AdminController::class,'display_scan']);

route::get('/scan_delete/{id}',[AdminController::class,'scan_delete']);

route::get('/scan_start/{id}',[AdminController::class,'scan_start']);

route::get('/scan_detail',[AdminController::class,'scan_detail']);

