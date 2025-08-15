<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ $title ?? 'Error' }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        @keyframes shake {
            0% {
                transform: rotate(-7deg);
            }

            100% {
                transform: rotate(7deg);
            }
        }

        .animate-shake {
            animation: shake 1s infinite alternate;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-slate-50 to-slate-200 text-gray-800 font-[Montserrat] min-h-screen flex items-center justify-center">

    <div class="bg-white px-10 py-12 rounded-3xl shadow-xl text-center max-w-sm">
        <div class="text-5xl mb-2 {{ $color }} animate-shake">{{ $icon }}</div>
        <div class="text-[7rem] font-bold tracking-wider {{ $color }} mb-2 drop-shadow-lg">{{ $code }}</div>
        <div class="text-lg mb-8 text-gray-600">
            {{ $message }}
        </div>
        <a href="{{ route('filament.admin.pages.dashboard') }}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-full shadow-md transition">
            Kembali ke dasbor
        </a>
    </div>

</body>

</html>
