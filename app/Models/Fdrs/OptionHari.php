<?php

namespace App\Models\Fdrs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OptionHari extends Model
{
    public $incrementing = false;
    protected $table = 'fdrs_option_hari';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'key',
        'name',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Str::orderedUuid();
        });
    }
}
