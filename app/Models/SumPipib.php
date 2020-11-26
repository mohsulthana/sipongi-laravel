<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;

class SumPipib extends Model
{
    use SoftDeletes, PostgisTrait;

    public $incrementing = false;
    protected $table = 'sum_pipib';
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

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Str::orderedUuid();
        });
    }
}
