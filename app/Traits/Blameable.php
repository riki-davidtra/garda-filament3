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
                $model->created_by = Auth::id();
            }

            $model->updated_by = null;
            $model->updated_at = null;
        });

        static::updating(function ($model) {
            if (Auth::check()) {
                $model->updated_by = Auth::id();
            }
        });

        if (in_array(SoftDeletes::class, class_uses_recursive(static::class))) {
            static::deleting(function ($model) {
                if (Auth::check()) {
                    $model->deleted_by = Auth::id();
                }

                if ($model->isFillable('restored_by')) {
                    $model->restored_by = null;
                }
                if ($model->isFillable('restored_at')) {
                    $model->restored_at = null;
                }

                $model->timestamps = false;
                $model->saveQuietly();
            });

            static::restoring(function ($model) {
                if (Auth::check() && $model->isFillable('restored_by')) {
                    $model->restored_by = Auth::id();
                }

                if ($model->isFillable('restored_at')) {
                    $model->restored_at = now();
                }

                $model->deleted_by = null;
                $model->deleted_at = null;
                $model->timestamps = false;
            });
        }
    }

    public function creator()
    {
        return $this->belongsTo(\App\Models\User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(\App\Models\User::class, 'updated_by');
    }

    public function deleter()
    {
        return $this->belongsTo(\App\Models\User::class, 'deleted_by');
    }

    public function restorer()
    {
        return $this->belongsTo(\App\Models\User::class, 'restored_by');
    }
}
