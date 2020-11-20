<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use MStaack\LaravelPostgis\Eloquent\PostgisTrait;

class Kelurahan extends Model
{
    use PostgisTrait;

    public $incrementing = false;
    protected $table = 'kelurahan';
    public $timestamps = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'kecamatan_id',
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

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class, 'kecamatan_id', 'id');
    }
}
