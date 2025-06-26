<?php

use App\Http\Controllers\FileEncryptionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;

Route::get('/view-log-500-server-error-xyz', function () {
    $logPath = storage_path('logs/laravel.log');

    if (!File::exists($logPath)) {
        return 'Log file not found.';
    }

    // Set the content type to plain text and return the log content
    return response(File::get($logPath), 200)
        ->header('Content-Type', 'text/plain');
});

Route::get('/', function () {
    return view('welcome');
});

Route::post('/encrypt', [FileEncryptionController::class, 'encrypt']);
Route::post('/decrypt', [FileEncryptionController::class, 'decrypt']);
