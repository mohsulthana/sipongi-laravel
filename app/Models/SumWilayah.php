<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SumWilayah extends Model
{
    use SoftDeletes;

    public $incrementing = false;
    protected $table = 'sum_wilayah';
    protected $keyType = 'string';

    protected $fillable = [
        'provinsi_id',
        'provinsi',
        'kabid',
        'kabkota',
        'kecid',
        'kecamatan',
        'desid',
        'desa',
        'nama',
        'jenis',
        'kategori',
        'meta',
        'kml',
        'sumber',
    ];

    protected $dates = ['deleted_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Str::orderedUuid();
        });
    }
}
