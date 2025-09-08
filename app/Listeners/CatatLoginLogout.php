<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\RiwayatAktivitas;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;

class CatatLoginLogout
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        $aksi = null;
        $user = $event->user;

        if ($event instanceof Login) {
            $aksi      = 'masuk';
            $deskripsi = 'User masuk ke dalam aplikasi';
        } elseif ($event instanceof Logout) {
            $aksi      = 'keluar';
            $deskripsi = 'User keluar dari aplikasi';
        }

        if ($aksi) {
            if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
                return;
            }

            RiwayatAktivitas::create([
                'user_id'     => $user->id,
                'aksi'        => $aksi,
                'jenis_data'  => 'Pengguna',
                'deskripsi'   => $deskripsi,
                'detail_data' => null,
                'ip'          => request()->ip(),
                'subjek_type' => \App\Models\User::class,
                'subjek_id'   => $user->id,
            ]);
        }
    }
}
