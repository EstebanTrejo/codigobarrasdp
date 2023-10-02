<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MiControlador;
use App\Http\Controllers\BarcodeController;

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

Route::get('/', function () {
    return view('welcome');
});


Route::get('/mostrar-vista', [MiControlador::class, 'mostrarVista'])->name('mostrar-vista');

Route::get('/barcode-form', [BarcodeController::class, 'showBarcodeForm'])->name('barcode-form');
Route::post('/generate-barcodes', [BarcodeController::class, 'generateBarcodes'])->name('generate-barcodes');
Route::post('/generate-pdf', [BarcodeController::class, 'generatePDF'])->name('generate-pdf');


