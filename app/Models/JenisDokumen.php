<?php

namespace App\Models;

use App\Traits\Blameable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisDokumen extends Model
{
    use HasFactory, HasUuids, SoftDeletes, Blameable;

    protected $guarded = [];

    protected $casts = [
        'dibuat_pada'     => 'datetime',
        'diperbarui_pada' => 'datetime',
        'dihapus_pada'    => 'datetime',
        'dipulihkan_pada' => 'datetime',

        'format_file' => 'array',
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

    public function jadwalDokumens()
    {
        return $this->hasMany(JadwalDokumen::class);
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
