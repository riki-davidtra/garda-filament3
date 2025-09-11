<?php

namespace App\Observers;

use App\Models\DataDukungPerencanaan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DataDukungPerencanaanObserver
{
    // public function creating(DataDukungPerencanaan $dataDukungPerencanaan): void
    // {
    //     $dataDukungPerencanaan->perubahan_ke = 1;
    // }

    // public function updating(DataDukungPerencanaan $dataDukungPerencanaan): void
    // {
    //     if ($dataDukungPerencanaan->isDirty()) {
    //         $dataDukungPerencanaan->perubahan_ke = $dataDukungPerencanaan->perubahan_ke + 1;
    //     }
    // }

    public function deleting(DataDukungPerencanaan $dataDukungPerencanaan): void
    {
        if ($dataDukungPerencanaan->isForceDeleting()) {
            $dataDukungPerencanaan->files()->withTrashed()->get()->each(function ($file) {
                $file->forceDelete();
            });
        } else {
            $dataDukungPerencanaan->files()->get()->each->delete();
        }
    }

    public function restoring(DataDukungPerencanaan $dataDukungPerencanaan): void
    {
        $dataDukungPerencanaan->files()->onlyTrashed()->get()->each->restore();
    }
}
