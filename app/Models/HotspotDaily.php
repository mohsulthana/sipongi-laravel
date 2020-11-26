<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class HotspotDaily extends Model
{
    use SoftDeletes;

    public $incrementing = false;
    protected $table = 'hotspot_daily';
    protected $keyType = 'string';

    protected $fillable = [
        'bulan',
        'provinsi_id',
        'sumber',
        't1',
        't2',
        't3',
        't4',
        't5',
        't6',
        't7',
        't8',
        't9',
        't10',
        't11',
        't12',
        't13',
        't14',
        't15',
        't16',
        't17',
        't18',
        't19',
        't20',
        't21',
        't22',
        't23',
        't24',
        't25',
        't26',
        't27',
        't28',
        't29',
        't30',
        't31'
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
