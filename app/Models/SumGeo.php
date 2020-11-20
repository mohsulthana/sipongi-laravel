<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;

class SumGeo extends Model
{
    use SoftDeletes, PostgisTrait;

    public $incrementing = false;
    protected $table = 'sum_geo';
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
        'geom',
    ];

    protected $postgisFields = [
        'geom'
    ];

    protected $postgisTypes = [
        'geom' => [
            'geomtype' => 'geometry',
            'srid' => 4326
        ]
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
