<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;

class SumCentroid extends Model
{
    use PostgisTrait, SoftDeletes;

    public $incrementing = false;
    protected $table = 'sum_centroid';
    protected $keyType = 'string';

    protected $fillable = [
        'provinsi_id',
        'provinsi',
        'kabid',
        'kabkota',
        'cp',
        'cka',
        'gcp',
        'gcka',
    ];

    protected $postgisFields = [
        'gcp',
        'gcka'
    ];

    protected $postgisTypes = [
        'gcp' => [
            'geomtype' => 'geometry',
            'srid' => 4326
        ],
        'gcka' => [
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
