<?php

namespace App\Observers;

use Illuminate\Support\Facades\Auth;
use App\Models\Pengaduan;

class PengaduanObserver
{
    public function creating(Pengaduan $pengaduan): void
    {
        if (empty($pengaduan->user_id)) {
            $pengaduan->user_id = Auth::user()->id;
        }
    }
}
