<?php

namespace App\Models\Passport;

use Illuminate\Support\Str;
use Laravel\Passport\PersonalAccessClient as passportPersonalAccessClient;

class PersonalAccessClient extends passportPersonalAccessClient
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
