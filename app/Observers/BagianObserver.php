<?php

namespace App\Observers;

use App\Models\Bagian;

class BagianObserver
{
    public function deleting(Bagian $bagian): void
    {
        $bagian->subbagians()->get()->each->delete();
    }
}
