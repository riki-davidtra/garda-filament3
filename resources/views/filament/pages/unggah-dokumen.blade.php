<x-filament-panels::page>
    <div class="grid grid-cols-4 gap-6">
        @foreach ($jenisDokumens as $jenis)
            @php
                $bisaUnggah = now()->between($jenis->waktu_unggah_mulai, $jenis->waktu_unggah_selesai);
            @endphp

            <div class="bg-white rounded-xl shadow p-6 flex flex-col items-center hover:shadow-lg transition">
                {{-- Icon File --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-blue-500 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m-6-8h6M4 6a2 2 0 012-2h7l5 5v9a2 2 0 01-2 2H6a2 2 0 01-2-2V6z" />
                </svg>

                {{-- Nama Jenis Dokumen --}}
                <h3 class="text-lg font-semibold text-gray-700 text-center">{{ $jenis->nama }}</h3>

                {{-- Waktu Unggah --}}
                <p class="text-sm text-gray-500 mt-1 text-center">
                    Waktu unggah: {{ $jenis->waktu_unggah_mulai->format('d M Y H:i') }}
                    s/d {{ $jenis->waktu_unggah_selesai->format('d M Y H:i') }}
                </p>

                {{-- Tombol Unggah --}}
                @if ($bisaUnggah)
                    <a href="{{ route('filament.admin.resources.dokumens.create', ['jenis_dokumen_id' => $jenis->id]) }}" class="mt-4 px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition">
                        Unggah
                    </a>
                @else
                    <span class="mt-4 px-4 py-2 bg-gray-300 text-gray-700 rounded-lg cursor-not-allowed">
                        Tidak bisa unggah sekarang
                    </span>
                @endif
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
