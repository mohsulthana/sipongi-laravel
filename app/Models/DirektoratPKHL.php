<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class DirektoratPKHL extends Model
{
    public $incrementing = false;
    protected $table = 'direktorat_pkhl';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'date',
        'text',
        'logo',
        'active',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Str::orderedUuid();
        });
    }

    public function getLogoUrlAttribute()
    {
        $fileexists = Storage::disk('publicNas')->exists("direktorat-pkhl/$this->logo");
        if ($fileexists) {
            return Storage::disk('publicNas')->url("direktorat-pkhl/$this->logo");
        } else {
            return Storage::disk('public')->url("no_image.png");
        }
    }
}
