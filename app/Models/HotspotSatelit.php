<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;

class HotspotSatelit extends Model
{
    use SoftDeletes, PostgisTrait;

    public $incrementing = false;
    protected $table = 'hotspot_satelit';
    protected $keyType = 'string';

    protected $fillable = [
        'date_hotspot',
        'x',
        'y',
        'sumber',
        'source',
        'confidence',
        'brightness',
        'provinsi_id',
        'provinsi',
        'kotakab_id',
        'kabkota',
        'kecamatan_id',
        'kecamatan',
        'kelurahan_id',
        'desa',
        'kawasan',
        'counter',
        'publikasi',
        'fungsi_kawasan',
        'sk_kawasan',
        'nama_hti',
        'nama_ha',
        'nama_kebun',
        'sumber2',
        'confidence_level',
        'tipe',
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

    protected $dates = ['deleted_at', 'date_hotspot'];

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
