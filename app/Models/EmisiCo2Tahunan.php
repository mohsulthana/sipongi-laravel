<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class EmisiCo2Tahunan extends Model
{
    use SoftDeletes;

    public $incrementing = false;
    protected $table = 'emisi_co2_tahunan';
    protected $keyType = 'string';

    protected $fillable = [
        'provinsi_id',
        'tahun',
        'total'
    ];

    protected $dates = ['deleted_at'];
    
    public function Provinsi()
    {
        return $this->belongsTo(Provinsi::class,'provinsi_id','id');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Str::orderedUuid();
        });
    }
}
