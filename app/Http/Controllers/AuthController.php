<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;


class AuthController extends Controller
{
    public function resetPasswordRequest()
    {
        return view('auth.reset-password-request');
    }

    public function resetPasswordSendRequest(Request $request)
    {
        $request->validate([
            'nomor_whatsapp' => 'required|string',
        ]);

        $user = User::where('nomor_whatsapp', $request->nomor_whatsapp)->first();

        if (! $user) {
            return back()->withErrors([
                'nomor_whatsapp' => 'Nomor WhatsApp tidak ditemukan.',
            ]);
        }

        $token = Password::createToken($user);

        $resetUrl = url(route('reset-password', [
            'token' => $token,
            'email' => $user->email,
        ], false));

        $message = "Halo {$user->name}, klik link reset password:\n$resetUrl";

        WhatsAppService::sendMessage($user->nomor_whatsapp, $message);

        return back()->with('message', 'Link reset password sudah dikirim ke WhatsApp Anda.');
    }

    public function resetPassword(string $token, Request $request)
    {
        // Cek token valid 
        $user = User::where('email', $request->email)->first();
        if (! $user || ! Password::tokenExists($user, $request->token)) {
            return redirect()->route('reset-password.request')->with(['message' => 'Token reset password tidak valid atau sudah digunakan.']);
        }

        // Ambil token dari tabel password_reset_tokens untuk cek created_at (expire)
        $tokenData     = DB::table(config('auth.passwords.users.table'))->where('email', $request->email)->first();
        $expireMinutes = config('auth.passwords.users.expire', 60);
        if (! $tokenData || Carbon::parse($tokenData->created_at)->addMinutes($expireMinutes)->isPast()) {
            return redirect()->route('reset-password.request')->with(['message' => 'Token reset password sudah kadaluwarsa. Silakan request ulang.']);
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->email,   // ambil email dari query string
        ]);
    }

    public function resetPasswordUpdate(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('filament.admin.pages.dashboard')->with('message', 'Kata sandi berhasil diubah.')
            :   back()->withErrors(['email' => __($status)]);
    }
}
