<?php

namespace App\Models\ManggalaAgni;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Sarpras extends Model
{
    public $incrementing = false;
    protected $table = 'manggala_agni_sarpras';
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
