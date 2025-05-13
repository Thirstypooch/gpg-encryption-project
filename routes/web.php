<?php

use App\Http\Controllers\FileEncryptionController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/encrypt', [FileEncryptionController::class, 'encrypt']);
Route::post('/decrypt', [FileEncryptionController::class, 'decrypt']);
