<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class StrukturOrganisasi extends Model
{
    public $incrementing = false;
    protected $table = 'struktur_organisasi';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'date',
        'image',
        'active',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Str::orderedUuid();
        });
    }

    public function getImageUrlAttribute()
    {
        $fileexists = Storage::disk('publicNas')->exists("struktur-organisasi/$this->image");
        if ($fileexists) {
            return Storage::disk('publicNas')->url("struktur-organisasi/$this->image");
        } else {
            return Storage::disk('public')->url("no_image.png");
        }
    }
}
