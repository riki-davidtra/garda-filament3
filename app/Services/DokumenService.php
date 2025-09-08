<?php

namespace App\Services;

use App\Models\Dokumen;
use App\Models\User;

class DokumenService
{
    /**
     * Ambil notifikasi untuk satu dokumen
     * 
     * @param Dokumen $dokumen
     * @return array [user_id => ['user' => User, 'pesan' => string lengkap]]
     */
    public static function notifikasiFind(Dokumen $dokumen): array
    {
        $users = User::whereHas('roles', fn($q) => $q->whereIn(
            'id',
            $dokumen->jenisDokumen->roles->pluck('id')
        ))->whereNotNull('nomor_whatsapp')->get();

        $notifikasi = [];

        foreach ($users as $user) {
            $pesanSingkat          = self::buatPesanStatus($dokumen);
            $notifikasi[$user->id] = [
                'user'  => $user,
                'pesan' => "Halo {$user->name}, berikut update dokumen:\n\n{$pesanSingkat}"
            ];
        }

        return $notifikasi;
    }

    /**
     * Buat pesan singkat berdasarkan status dokumen
     */
    public static function buatPesanStatus(Dokumen $dokumen): string
    {
        $jenis    = $dokumen->jenisDokumen->nama;
        $status   = $dokumen->status;
        $komentar = $dokumen->komentar ?? '-';

        $statusLabel = match ($status) {
            'Menunggu Persetujuan'        => '⌛ Menunggu Persetujuan',
            'Diterima'                    => '✅ Diterima',
            'Ditolak'                     => '❌ Ditolak',
            'Revisi Menunggu Persetujuan' => '⌛ Revisi Menunggu Persetujuan',
            'Revisi Diterima'             => '✅ Revisi Diterima',
            'Revisi Ditolak'              => '❌ Revisi Ditolak',
            default                       => 'ℹ️ Status Tidak Diketahui',
        };

        return "📄 *{$jenis}*\nStatus: {$statusLabel}\nKomentar: {$komentar}";
    }
}
