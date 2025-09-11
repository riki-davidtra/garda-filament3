<?php

namespace App\Models;

use App\Traits\HasRiwayatAktivitas;
use App\Traits\Blameable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Ramsey\Uuid\Uuid;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataDukungPerencanaan extends Model
{
    use HasFactory, HasUuids, SoftDeletes, Blameable, HasRiwayatAktivitas;

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

    public function files()
    {
        return $this->morphMany(File::class, 'model');
    }

    public function getReadableAttributes(): array
    {
        $attrs = $this->getAttributes();

        $relationsMap = [
            'dibuat_oleh'     => ['relation' => 'pembuat', 'column' => 'name'],
            'diperbarui_oleh' => ['relation' => 'pembaru', 'column' => 'name'],
            'dihapus_oleh'    => ['relation' => 'penghapus', 'column' => 'name'],
            'dipulihkan_oleh' => ['relation' => 'pemulih', 'column' => 'name'],
        ];

        foreach ($relationsMap as $field => $config) {
            if (isset($attrs[$field])) {
                $related       = $this->{$config['relation']};
                $attrs[$field] = [
                    'id'    => $attrs[$field],
                    'label' => $related?->{$config['column']} ?? $attrs[$field],
                ];
            }
        }

        return $attrs;
    }
}
