<?php

namespace App\Models\Publikasi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GaleriDetail extends Model
{
    use SoftDeletes;

    public $incrementing = false;
    protected $table = 'pub_galeri_detail';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'galeri_id',
        'slug',
        'keterangan',
        'image',
        'publish',
    ];

    protected $dates = ['deleted_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Str::orderedUuid();

            $model->slug = Str::slug(Str::lower($model->keterangan));

            $latestSlug =
                static::whereRaw("slug = '$model->slug' or slug LIKE '$model->slug-%'")
                ->latest('slug')
                ->value('slug');
            if ($latestSlug) {
                $pieces = explode('-', $latestSlug);

                $number = (int) end($pieces);

                $model->slug .= '-' . ($number + 1);
            }
        });

        static::updating(function ($model) {
            if ($model->keterangan != $model->getOriginal('keterangan')) {
                $model->slug = Str::slug(Str::lower($model->keterangan));

                $latestSlug =
                    static::whereRaw("(slug = '$model->slug' or slug LIKE '$model->slug-%') and id != '$model->id'")
                    ->latest('slug')
                    ->value('slug');
                if ($latestSlug) {
                    $pieces = explode('-', $latestSlug);

                    $number = (int) end($pieces);

                    $model->slug .= '-' . ($number + 1);
                }
            } else {
                $model->slug = $model->getOriginal('slug');
            }
        });
    }

    public function galeri()
    {
        return $this->belongsTo(Galeri::class, 'galeri_id', 'id');
    }

    public function getImageUrlAttribute()
    {
        $fileexists = Storage::disk('publicNas')->exists("galeri/$this->galeri_id/images/$this->image");
        if ($fileexists) {
            return Storage::disk('publicNas')->url("galeri/$this->galeri_id/images/$this->image");
        } else {
            return Storage::disk('public')->url("no_image.png");
        }
    }
}
