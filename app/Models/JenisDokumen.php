<?php

namespace App\Models;

use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisDokumen extends Model
{
    use HasFactory, HasUuids;

    protected $guarded = [];

    protected $casts = [
        'waktu_unggah_mulai'   => 'datetime',
        'waktu_unggah_selesai' => 'datetime',
        'format_file'          => 'array',
    ];

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

    public function dokumens()
    {
        return $this->hasMany(Dokumen::class);
    }

    public function roles()
    {
        return $this->belongsToMany(\Spatie\Permission\Models\Role::class, 'jenis_dokumen_roles');
    }

    public function templatDokumen()
    {
        return $this->hasOne(TemplatDokumen::class);
    }
}
