<?php

namespace App\Models\ManggalaAgni;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Profil extends Model
{
    public $incrementing = false;
    protected $table = 'manggala_agni_profil';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'title',
        'text',
        'urutan',
        'image',
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
        $fileexists = Storage::disk('publicNas')->exists("manggala-agni/$this->image");
        if ($fileexists) {
            return Storage::disk('publicNas')->url("manggala-agni/$this->image");
        } else {
            return Storage::disk('public')->url("no_image.png");
        }
    }
}
