<?php

namespace App\Traits;

use App\Models\RiwayatAktivitas;
use Illuminate\Support\Facades\Auth;

trait HasRiwayatAktivitas
{
    public static function bootHasRiwayatAktivitas()
    {
        static::created(function ($model) {
            self::buatRiwayat($model, 'buat', "User membuat data.");
        });

        static::updated(function ($model) {
            if ($model->wasChanged('deleted_at') && is_null($model->deleted_at)) {
                return;
            }

            $before = $model->getReadableAttributes($model->getOriginal());

            $afterAll = $model->getReadableAttributes($model->getAttributes());
            $dirtyKeys = array_keys($model->getDirty());
            $after = array_intersect_key($afterAll, array_flip($dirtyKeys));

            $changes = [
                'before' => $before,
                'after'  => $after,
            ];

            self::buatRiwayat($model, 'ubah', "User memperbarui data.", $changes);
        });

        static::deleted(function ($model) {
            $aksi = $model->isForceDeleting() ? 'hapus permanen' : 'hapus';
            self::buatRiwayat($model, $aksi, "User meng{$aksi} data.");
        });

        static::restored(function ($model) {
            self::buatRiwayat($model, 'pulihkan', "User memulihkan data.");
        });
    }

    protected static function buatRiwayat($model, $aksi, $deskripsi, $detail = null)
    {
        if (app()->runningInConsole()) {
            return;
        }

        $user = Auth::user();

        if (! $user || (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin())) {
            return;
        }

        if ($detail) {
            $attributes = $detail;
        } elseif (method_exists($model, 'getReadableAttributes')) {
            $attributes = $model->getReadableAttributes();
        } else {
            $attributes = $model->getAttributes();
        }

        RiwayatAktivitas::create([
            'user_id'     => $user?->id,
            'aksi'        => $aksi,
            'jenis_data'  => class_basename($model),
            'deskripsi'   => $deskripsi,
            'detail_data' => json_encode($attributes, JSON_PRETTY_PRINT),
            'ip'          => request()->ip(),
            'subjek_type' => get_class($model),
            'subjek_id'   => $model->id,
        ]);
    }
}
