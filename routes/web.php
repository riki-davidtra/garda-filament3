<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use App\Models\FileDokumen;
use App\Models\DataDukungPerencanaan;
use App\Models\TemplatDokumen;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return redirect()->route('filament.admin.pages.dashboard');
});

Route::get('/file-dokumen/unduh/{id}', function ($id) {
    $file             = FileDokumen::findOrFail($id);
    $encrypted        = Storage::disk('local')->get($file->path);
    $decryptedContent = decrypt($encrypted);
    $fileName         = basename($file->path);
    return response($decryptedContent)
        ->header('Content-Type', 'application/octet-stream')
        ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
})->name('file-dokumen.unduh');

Route::get('/file-data-dukung-perencanaan/unduh/{id}', function ($id) {
    $file             = DataDukungPerencanaan::findOrFail($id);
    $encrypted        = Storage::disk('local')->get($file->path);
    $decryptedContent = decrypt($encrypted);
    $fileName         = basename($file->path);
    return response($decryptedContent)
        ->header('Content-Type', 'application/octet-stream')
        ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
})->name('file-data-dukung-perencanaan.unduh');

Route::get('/template/unduh/{id}', function ($id) {
    $template = TemplatDokumen::findOrFail($id);
    $file = Storage::disk('public')->get($template->path);
    $fileName         = basename($template->path);
    return response($file)
        ->header('Content-Type', 'application/octet-stream')
        ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
})->name('template.unduh');

Route::get('/test-404', fn() => abort(404));
Route::get('/test-403', fn() => abort(403));
Route::get('/test-500', fn() => abort(500));
