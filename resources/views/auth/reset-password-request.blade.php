@extends('layouts.auth.app')

@push('title', 'Reset Password Request')

@section('content')
    <div class="max-w-md mx-auto mt-10 bg-white p-6 rounded shadow">
        <h2 class="text-xl font-bold mb-4">Reset Password via WhatsApp</h2>

        @if (session('message'))
            <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-800 rounded">
                {{ session('message') }}
            </div>
        @endif

        <form method="POST" action="{{ route('reset-password.send-request') }}">
            @csrf
            <div class="mb-4">
                <label for="nomor_whatsapp" class="block text-gray-700">Nomor WhatsApp</label>
                <input id="nomor_whatsapp" type="number" name="nomor_whatsapp" class="mt-1 block w-full border rounded px-3 py-2" placeholder="628xxxxxxxxxx" required>
                @error('nomor_whatsapp')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="w-full bg-green-500 hover:bg-green-600 text-white font-semibold py-2 rounded">
                Kirim Link Reset
            </button>
        </form>
    </div>
@endsection
