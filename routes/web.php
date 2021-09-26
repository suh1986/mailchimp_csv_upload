<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('file-import-export', [App\Http\Controllers\ImportController::class, 'fileImportExport']);
// Route::post('file-import', [App\Http\Controllers\ImportController::class, 'fileImport'])->name('file-import');
Route::get('file-export', [ImportController::class, 'fileExport'])->name('file-export');
