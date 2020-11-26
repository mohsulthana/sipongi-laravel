<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;

class SumLhnTutupan extends Model
{
    use SoftDeletes, PostgisTrait;

    public $incrementing = false;
    protected $table = 'sum_lhn_tutupan';
    protected $keyType = 'string';

    protected $fillable = [
        'provinsi_id',
        'provinsi',
        'kotakab_id',
        'kabkota',
        'kecamatan_id',
        'kecamatan',
        'kelurahan_id',
        'desa',
        'nama',
        'jenis',
        'kategori',
        'meta',
        'kml',
        'sumber',
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
