@extends('layouts.auth.app')

@push('title', 'Reset Password')

@section('content')
    <div class="max-w-md mx-auto mt-10 bg-white p-6 rounded shadow">
        <h2 class="text-xl font-bold mb-4">Ubah Kata Sandi</h2>

        @if (session('message'))
            <div class="mb-4 p-3 bg-green-100 border border-green-300 text-green-800 rounded">
                {{ session('message') }}
            </div>
        @endif

        <form method="POST" action="{{ route('reset-password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="email" value="{{ request('email') }}">

            <div class="mb-4">
                <label for="password" class="block text-gray-700">Kata Sandi Baru</label>
                <input id="password" type="password" name="password" class="mt-1 block w-full border rounded px-3 py-2" required>
                @error('password')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <div class="mb-4">
                <label for="password_confirmation" class="block text-gray-700">Konfirmasi Kata Sandi</label>
                <input id="password_confirmation" type="password" name="password_confirmation" class="mt-1 block w-full border rounded px-3 py-2" required>
                @error('password_confirmation')
                    <span class="text-red-600 text-sm">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="w-full bg-blue-500 hover:bg-blue-600 text-white font-semibold py-2 rounded">
                Simpan Kata Sandi
            </button>
        </form>
    </div>
@endsection
