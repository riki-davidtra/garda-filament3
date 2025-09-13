<?php

namespace App\Services;

use App\Models\JadwalDokumen;
use App\Models\User;
use Carbon\Carbon;

class JadwalDokumenService
{
    public static function notifikasiAll(): array
    {
        $now = Carbon::now();

        $jadwals = [
            'waktu_tidak_ditentukan' => JadwalDokumen::whereNull('waktu_unggah_mulai')
                ->whereNull('waktu_unggah_selesai')
                ->where('aktif', true)->with('jenisDokumen.roles')->get(),

            'akan_mulai' => JadwalDokumen::where('waktu_unggah_mulai', '>', $now)
                ->where('waktu_unggah_mulai', '<=', $now->copy()->addDays(3)->endOfDay())
                ->where('aktif', true)->with('jenisDokumen.roles')->get(),

            'sedang_berlangsung' => JadwalDokumen::where('waktu_unggah_mulai', '<=', $now)
                ->where('waktu_unggah_selesai', '>=', $now)
                ->where('aktif', true)->with('jenisDokumen.roles')->get(),

            'akan_selesai' => JadwalDokumen::where('waktu_unggah_selesai', '>', $now)
                ->where('waktu_unggah_selesai', '<=', $now->copy()->addDays(3)->endOfDay())
                ->where('aktif', true)->with('jenisDokumen.roles')->get(),

            'sudah_selesai' => JadwalDokumen::where('waktu_unggah_selesai', '<', $now)
                ->where('aktif', true)->with('jenisDokumen.roles')->get(),
        ];

        $notifikasi = [];
        $baseUrl    = config('app.url');

        foreach ($jadwals as $status => $list) {
            foreach ($list as $jadwal) {
                $users = User::whereHas('roles', fn($q) => $q->whereIn(
                    'id',
                    $jadwal->jenisDokumen->roles->pluck('id')
                ))->whereNotNull('nomor_whatsapp')->get();

                foreach ($users as $user) {
                    $notifikasi[$user->id]['user']    = $user;
                    $notifikasi[$user->id]['pesan'][] = self::buatPesanSingkat($status, $jadwal);
                }
            }
        }

        foreach ($notifikasi as $userId => $data) {
            $user                         = $data['user'];
            $daftarPesan                  = implode("\n\n", $data['pesan']);
            $notifikasi[$userId]['pesan'] = "Halo {$user->name}, berikut daftar update jadwal dokumen:\n\n{$daftarPesan}\n\n{$baseUrl}";
        }

        return $notifikasi;
    }

    public static function notifikasiFind(JadwalDokumen $jadwal): array
    {
        $now = Carbon::now();

        if (is_null($jadwal->waktu_unggah_mulai) && is_null($jadwal->waktu_unggah_selesai)) {
            $status = 'waktu_tidak_ditentukan';
        } elseif ($jadwal->waktu_unggah_mulai > $now) {
            $status = 'akan_mulai';
        } elseif ($jadwal->waktu_unggah_selesai < $now) {
            $status = 'sudah_selesai';
        } elseif ($jadwal->waktu_unggah_mulai <= $now && $jadwal->waktu_unggah_selesai >= $now) {
            $status = 'sedang_berlangsung';
        } else {
            $status = 'akan_selesai';
        }

        $users = User::whereHas('roles', fn($q) => $q->whereIn(
            'id',
            $jadwal->jenisDokumen->roles->pluck('id')
        ))->whereNotNull('nomor_whatsapp')->get();

        $notifikasi = [];
        $baseUrl    = config('app.url');

        foreach ($users as $user) {
            $pesanSingkat          = self::buatPesanSingkat($status, $jadwal);
            $notifikasi[$user->id] = [
                'user'  => $user,
                'pesan' => "Halo {$user->name}, berikut update jadwal dokumen:\n\n{$pesanSingkat}\n\n{$baseUrl}"
            ];
        }

        return $notifikasi;
    }

    public static function buatPesanSingkat(string $status, JadwalDokumen $jadwal): string
    {
        $jenis   = $jadwal->jenisDokumen->nama;
        $mulai   = $jadwal->waktu_unggah_mulai?->format('d-m-Y H:i') ?? '-';
        $selesai = $jadwal->waktu_unggah_selesai?->format('d-m-Y H:i') ?? '-';

        if ($status === 'waktu_tidak_ditentukan') {
            return "📭 *{$jenis}* dengan waktu yang tidak ditentukan.";
        }

        if ($status === 'akan_mulai') {
            $diff = Carbon::now()->diff($jadwal->waktu_unggah_mulai, false);
            return "📌 *{$jenis}* akan dimulai *{$diff->d} hari {$diff->h} jam lagi*.\nMulai: {$mulai}\nSelesai: {$selesai}";
        }

        if ($status === 'akan_selesai') {
            $diff = Carbon::now()->diff($jadwal->waktu_unggah_selesai, false);
            return "⚠️ *{$jenis}* akan berakhir *{$diff->d} hari {$diff->h} jam lagi*.\nBatas unggah: {$selesai}";
        }

        return match ($status) {
            'sedang_berlangsung' => "⏳ *{$jenis}* sedang berlangsung.\nMulai: {$mulai}\nSelesai: {$selesai}",
            'sudah_selesai'      => "✅ *{$jenis}* telah berakhir.\nMulai: {$mulai}\nSelesai: {$selesai}",
            default              => "*{$jenis}* ada update jadwal.",
        };
    }
}
