<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;

class KotaKab extends Model
{
    use PostgisTrait;

    public $incrementing = false;
    protected $table = 'kotakab';
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'provinsi_id',
        'nama',
        'geom',
        'meta',
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

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class, 'provinsi_id', 'id');
    }

    public function kecamatan()
    {
        return $this->hasMany(Kecamatan::class, 'kotakab_id', 'id');
    }
}
