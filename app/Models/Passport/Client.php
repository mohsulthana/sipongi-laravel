<?php

namespace App\Models\Passport;

use Illuminate\Support\Str;
use Laravel\Passport\Client as passportClient;

class Client extends passportClient
{
    public $incrementing = false;

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Str::orderedUuid();
        });
    }
}
