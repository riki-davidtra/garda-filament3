<?php

namespace App\Models;

use App\Traits\Blameable;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplatDokumen extends Model
{
    use HasFactory, HasUuids, Blameable;

    protected $guarded = [];

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

    public function jenisDokumen()
    {
        return $this->belongsTo(JenisDokumen::class);
    }

    public function files()
    {
        return $this->morphMany(File::class, 'model');
    }
}
