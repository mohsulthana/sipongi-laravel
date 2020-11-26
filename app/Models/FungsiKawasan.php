<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;

class FungsiKawasan extends Model
{
    use PostgisTrait, SoftDeletes;

    public $incrementing = false;
    protected $table = 'fungsi_kawasan';
    protected $keyType = 'string';

    protected $fillable = [
        'placemark_id',
        'provinsi_id',
        'provinsi',
        'kotakab_id',
        'kabkota',
        'kecamatan_id',
        'kecamatan',
        'kelurahan_id',
        'desa',
        'kawasan',
        'nama_hti',
        'nama_ha',
        'nama_kebun',
        'provinsi_id',
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


    public function provinsi_rel()
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id', 'id');
    }
}
