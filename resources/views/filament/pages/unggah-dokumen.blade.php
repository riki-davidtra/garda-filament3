<x-filament-panels::page>
    <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-3">
        @foreach ($jenisDokumens as $jenis)
            @php
                $sekarang = now();
                $belumMulai = $sekarang->lt($jenis->waktu_unggah_mulai);
                $sudahBerakhir = $sekarang->gt($jenis->waktu_unggah_selesai);
                $bisaUnggah = $sekarang->between($jenis->waktu_unggah_mulai, $jenis->waktu_unggah_selesai);

                $user = Auth::user();
                $isAdmin = $user->hasRole('Super Admin') || $user->hasRole('Admin');
            @endphp

            <div class="bg-white rounded-2xl shadow-sm p-6 flex flex-col items-center text-center border border-gray-100 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                <div class="flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m-6-8h6M4 6a2 2 0 012-2h7l5 5v9a2 2 0 01-2 2H6a2 2 0 01-2-2V6z" />
                    </svg>
                </div>

                <h3 class="text-lg font-semibold text-gray-800">{{ $jenis->nama }}</h3>

                <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                    {{ $jenis->waktu_unggah_mulai->locale('id')->translatedFormat('d M Y H:i') }}
                    <span class="text-gray-400">s/d</span>
                    {{ $jenis->waktu_unggah_selesai->locale('id')->translatedFormat('d M Y H:i') }}
                </p>

                @if ($bisaUnggah || $isAdmin)
                    <a href="{{ route('filament.admin.resources.dokumens.create', ['jenis_dokumen_id' => $jenis->id]) }}" class="mt-5 inline-block px-5 py-2.5 bg-blue-500 text-white text-sm font-medium rounded-full shadow hover:bg-blue-600 focus:ring-2 focus:ring-blue-300 focus:outline-none transition">
                        Unggah
                    </a>
                @elseif ($belumMulai)
                    <span class="cursor-not-allowed mt-5 inline-block px-5 py-2.5 bg-yellow-100 text-yellow-800 text-sm font-medium rounded-full">
                        Belum masuk waktu unggah
                    </span>
                @elseif ($sudahBerakhir)
                    <span class="cursor-not-allowed mt-5 inline-block px-5 py-2.5 bg-red-100 text-red-800 text-sm font-medium rounded-full">
                        Waktu unggah berakhir
                    </span>
                @endif
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
