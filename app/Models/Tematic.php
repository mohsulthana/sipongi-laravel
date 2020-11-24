<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Tematic extends Model
{
    use SoftDeletes;

    public $incrementing = false;
    protected $table = 'tematic';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'title',
        'image',
	'publish',
    ];

    protected $dates = ['deleted_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Str::orderedUuid();
        });

        static::updating(function ($model) {
            
        });
    }

    public function getImageUrlAttribute()
    {
        $fileexists = Storage::disk('publicNas')->exists("tematic/$this->id/images/$this->image");
        if ($fileexists) {
            return Storage::disk('publicNas')->url("tematic/$this->id/images/$this->image");
        } else {
            return Storage::disk('public')->url("no_image.png");
        }
    }
}

