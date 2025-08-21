<?php

namespace App\Models;

use App\Traits\Blameable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndeksKinerjaUtama extends Model
{
    use HasFactory, HasUuids, SoftDeletes, Blameable;

    protected $guarded = [];

    protected $casts = [
        'dibuat_pada'     => 'datetime',
        'diperbarui_pada' => 'datetime',
        'dihapus_pada'    => 'datetime',
        'dipulihkan_pada' => 'datetime',
    ];

    protected $dates = ['deleted_at'];

    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function newUniqueId(): string
    {
        return (string) Uuid::uuid7();
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function indikator()
    {
        return $this->belongsTo(Indikator::class);
    }
}
