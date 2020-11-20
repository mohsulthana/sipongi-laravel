<?php

namespace App\Models\ManggalaAgni;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DaerahOperasi extends Model
{
    public $incrementing = false;
    protected $table = 'manggala_agni_daerah_operasi';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'parent_id',
        'status',
        'daerah',
        'telepon',
        'alamat',
        'jumlah_regu',
        'jumlah_anggota',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Str::orderedUuid();
        });
    }
}
