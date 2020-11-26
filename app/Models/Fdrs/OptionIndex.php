<?php

namespace App\Models\Fdrs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OptionIndex extends Model
{
    public $incrementing = false;
    protected $table = 'fdrs_option_index';
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
