<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\SoftDeletes;

trait Blameable
{
    public static function bootBlameable()
    {
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->dibuat_oleh = Auth::id();
                $model->dibuat_pada = now();
            }
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->diperbarui_oleh = Auth::id();
                $model->diperbarui_pada = now();
            }
        });

        if (in_array(SoftDeletes::class, class_uses_recursive(static::class))) {
            static::deleting(function ($model) {
                if (Auth::check()) {
                    $model->dihapus_oleh = Auth::id();
                    $model->dihapus_pada = now();
                    $model->saveQuietly();
                }
            });

            static::restoring(function ($model) {
                if (Auth::check()) {
                    if ($model->isFillable('dipulihkan_oleh')) {
                        $model->dipulihkan_oleh = Auth::id();
                    }
                    if ($model->isFillable('dipulihkan_pada')) {
                        $model->dipulihkan_pada = now();
                    }
                    $model->saveQuietly();
                }
            });
        }
    }

    public function pembuat()
    {
        return $this->belongsTo(\App\Models\User::class, 'dibuat_oleh');
    }

    public function pembaru()
    {
        return $this->belongsTo(\App\Models\User::class, 'diperbarui_oleh');
    }

    public function penghapus()
    {
        return $this->belongsTo(\App\Models\User::class, 'dihapus_oleh');
    }

    public function pemulih()
    {
        return $this->belongsTo(\App\Models\User::class, 'dipulihkan_oleh');
    }
}
