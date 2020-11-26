<?php

namespace App\Models;

use App\Models\Spatie\Permission;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Module extends Model
{
    public $incrementing = false;

    protected $table = 'modules';
    protected $keyType = 'string';

    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Str::orderedUuid();
        });
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class)->orderBy('order');
    }
}
