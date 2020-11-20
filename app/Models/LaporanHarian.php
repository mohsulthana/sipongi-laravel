<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LaporanHarian extends Model
{
    public $incrementing = false;
    protected $table = 'laporan_harian';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'bulan',
        'bulan_nama',
        'tahun',
        'link',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Str::orderedUuid();
        });
    }
}
