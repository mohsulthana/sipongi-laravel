<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;

class Kecamatan extends Model
{
    use PostgisTrait;

    public $incrementing = false;
    protected $table = 'kecamatan';
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'kotakab_id',
        'nama',
        'geom'
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
        return $this->belongsTo(KotaKab::class, 'kotakab_id', 'id');
    }

    public function kelurahan()
    {
        return $this->hasMany(Kelurahan::class, 'kecamatan_id', 'id');
    }
}
