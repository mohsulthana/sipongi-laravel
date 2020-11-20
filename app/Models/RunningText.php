<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RunningText extends Model
{
    public $incrementing = false;
    protected $table = 'running_text';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
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
