<?php

namespace App\Models;

use App\Traits\Blameable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileDokumen extends Model
{
    use HasFactory, HasUuids, SoftDeletes, Blameable;

    protected $guarded = [];

    protected $casts = [
        'deleted_at'  => 'datetime',
        'restored_at' => 'datetime',
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

    public function dokumen()
    {
        return $this->belongsTo(Dokumen::class);
    }
}
