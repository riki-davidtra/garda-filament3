<div>
    @php
        $template = $getRecord()->jenisDokumen?->templatDokumen;
    @endphp
    @if ($template && $template->path && Storage::disk('public')->exists($template->path))
        <a href="{{ Storage::url($getState()) }}" download class="inline-flex items-center gap-1 px-2 py-1 rounded-md bg-blue-50 text-blue-600 text-xs font-medium hover:underline">
            📄 Unduh Template
        </a>
    @else
        <span class="text-xs text-gray-500 italic">Template belum tersedia</span>
    @endif
</div>
