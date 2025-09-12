<div>
    @php
        $templateDokumen = $getRecord()->jenisDokumen?->templatDokumen;
        $fileTerbaru = $templateDokumen->files()->latest()->first();
    @endphp

    @if ($fileTerbaru && $fileTerbaru->path && Storage::disk('local')->exists($fileTerbaru->path))
        <a href="{{ $fileTerbaru ? route('file-templat-dokumen.unduh', $fileTerbaru->id) : '#' }}" download class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-blue-50 text-blue-600 text-xs font-medium hover:underline">
            📄 Unduh Template
        </a>
    @else
        <span class="text-xs text-gray-500 italic">Template belum tersedia</span>
    @endif
</div>
