<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\FileDokumen;
use App\Models\DataDukungPerencanaan;
use App\Models\TemplatDokumen;
use App\Models\IndeksKinerjaUtama;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return redirect()->route('filament.admin.pages.dashboard');
});

Route::get('/file-dokumen/unduh/{id}', function ($id) {
    $record           = FileDokumen::findOrFail($id);
    $encrypted        = Storage::disk('local')->get($record->path);
    $decryptedContent = decrypt($encrypted);
    $fileName         = basename($record->path);
    return response($decryptedContent)
        ->header('Content-Type', 'application/octet-stream')
        ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
})->name('file-dokumen.unduh');

Route::get('/file-data-dukung-perencanaan/unduh/{id}', function ($id) {
    $record           = DataDukungPerencanaan::findOrFail($id);
    $encrypted        = Storage::disk('local')->get($record->path);
    $decryptedContent = decrypt($encrypted);
    $fileName         = basename($record->path);
    return response($decryptedContent)
        ->header('Content-Type', 'application/octet-stream')
        ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
})->name('file-data-dukung-perencanaan.unduh');

Route::get('/template/unduh/{id}', function ($id) {
    $record   = TemplatDokumen::findOrFail($id);
    $file     = Storage::disk('public')->get($record->path);
    $fileName = basename($record->path);
    return response($file)
        ->header('Content-Type', 'application/octet-stream')
        ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
})->name('template.unduh');

Route::get('/iku/unduh/{id}', function ($id) {
    $record = IndeksKinerjaUtama::findOrFail($id);
    $pdf    = Pdf::loadView('pdf.iku', [
        'record' => $record,
    ])->setPaper('a4', 'landscape');
    $indikator = Str::slug($record->indikator?->nama ?? 'indikator');
    $periode   = Str::slug($record->periode ?? 'periode');
    $tahun     = $record->tahun ?? date('Y');
    $fileName  = "iku-{$indikator}-{$periode}-{$tahun}.pdf";
    return response()->streamDownload(
        fn() => print($pdf->output()),
        $fileName
    );
})->name('iku.unduh');

Route::get('/test-404', fn() => abort(404));
Route::get('/test-403', fn() => abort(403));
Route::get('/test-500', fn() => abort(500));
