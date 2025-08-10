<?php

use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return redirect()->route('filament.admin.pages.dashboard');
});

Route::get('/file-dokumen/unduh/{id}', function ($id) {
    $file             = \App\Models\FileDokumen::findOrFail($id);
    $encrypted        = \Illuminate\Support\Facades\Storage::disk('local')->get($file->path);
    $decryptedContent = decrypt($encrypted);
    $fileName         = basename($file->path);
    return response($decryptedContent)
        ->header('Content-Type', 'application/octet-stream')
        ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
})->name('file-dokumen.unduh');
