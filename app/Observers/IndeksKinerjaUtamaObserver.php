<?php

namespace App\Observers;

use App\Models\IndeksKinerjaUtama;
use Illuminate\Support\Facades\Auth;

class IndeksKinerjaUtamaObserver
{
    public function updating(IndeksKinerjaUtama $indeksKinerjaUtama): void
    {
        if ($indeksKinerjaUtama->isDirty()) {
            $indeksKinerjaUtama->perubahan_ke = ($indeksKinerjaUtama->perubahan_ke ?? 1) + 1;
        }
    }
}
