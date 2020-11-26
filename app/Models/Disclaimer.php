<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Disclaimer extends Model
{
    public $incrementing = false;
    protected $table = 'disclaimer';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'date',
        'text',
        'active',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Str::orderedUuid();
        });
    }
}
