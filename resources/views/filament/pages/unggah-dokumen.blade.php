<x-filament-panels::page>
    <div class="grid grid-cols-4 gap-6">
        @foreach ($jenisDokumens as $jenis)
            <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center hover:shadow-lg transition">
                {{-- Icon File --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m-6-8h6M4 6a2 2 0 012-2h7l5 5v9a2 2 0 01-2 2H6a2 2 0 01-2-2V6z" />
                </svg>

                {{-- Nama Jenis Dokumen --}}
                <h3 class="text-lg font-semibold text-gray-700 text-center">{{ $jenis->nama }}</h3>

                {{-- Tombol Unggah --}}
                <a href="{{ route('filament.admin.resources.dokumens.create', ['jenis_dokumen_id' => $jenis->id]) }}" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                    Unggah
                </a>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
