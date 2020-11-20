<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;

class RawGeoKabKota extends Model
{
    use PostgisTrait, SoftDeletes;

    public $incrementing = false;
    protected $table = 'raw_geo_kabkota';
    protected $keyType = 'string';

    protected $fillable = [
        'provinsi_id',
        'kabid',
        'nama',
        'geom',
        'meta',
        'sumberid',
        'sumber'
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
