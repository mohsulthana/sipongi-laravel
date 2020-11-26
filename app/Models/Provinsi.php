<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;

class Provinsi extends Model
{
    use PostgisTrait;

    public $incrementing = false;
    protected $table = 'provinsi';
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'nama_provinsi',
        'pulau',
        'x',
        'y',
        'sort',
        'regional_id',
        'old_id',
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

    public function kota_kab()
    {
        return $this->hasMany(KotaKab::class, 'provinsi_id', 'id');
    }
}
