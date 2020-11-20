<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;

class PetaKawasan extends Model
{
    use PostgisTrait, SoftDeletes;

    public $incrementing = false;
    protected $table = 'peta_kawasan';
    protected $keyType = 'string';

    protected $fillable = [
        'placemark_id',
        'placemark_name',
        'fungsi',
        'fungsi_kawasan',
        'sk_kawasan',
        'tgl_kawasan',
        'luas_kawasan',
        'dpcls',
        'keterangan',
        'provinsi_id',
        'provinsi',
        'shape_leng',
        'shape_area',
        'poligon',
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
