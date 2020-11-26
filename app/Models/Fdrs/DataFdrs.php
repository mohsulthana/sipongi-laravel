<?php

namespace App\Models\Fdrs;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class DataFdrs extends Model
{
    public $incrementing = false;
    protected $table = 'fdrs_data';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'fdrs_option_wilayah_key',
        'fdrs_option_index_key',
        'fdrs_option_hari_key',
        'date',
        'image'
    ];

    public function getImageUrlAttribute()
    {
        $fileexists = Storage::disk('publicNas')->exists("fdrs/$this->fdrs_option_wilayah_key/$this->image");
        if ($fileexists) {
            return Storage::disk('publicNas')->url("fdrs/$this->fdrs_option_wilayah_key/$this->image");
        } else {
            return Storage::disk('public')->url("no_image.png");
        }
    }

    public function Wilayah()
    {
        return $this->belongsTo(OptionWilayah::class,'fdrs_option_wilayah_key','key');
    }

    public function Index()
    {
        return $this->belongsTo(OptionIndex::class,'fdrs_option_index_key','key');
    }

    public function Hari()
    {
        return $this->belongsTo(OptionHari::class,'fdrs_option_hari_key','key');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Str::orderedUuid();
        });
    }
}
