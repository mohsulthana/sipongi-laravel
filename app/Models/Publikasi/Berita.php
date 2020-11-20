<?php

namespace App\Models\Publikasi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Berita extends Model
{
    use SoftDeletes;

    public $incrementing = false;
    protected $table = 'berita';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'slug',
        'title',
        'desc',
        'image',
        'publish',
    ];

    protected $dates = ['deleted_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Str::orderedUuid();

            $model->slug = Str::slug(Str::lower($model->title));

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
            if ($model->title != $model->getOriginal('title')) {
                $model->slug = Str::slug(Str::lower($model->title));

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

    public function getImageUrlAttribute()
    {
        $fileexists = Storage::disk('publicNas')->exists("berita/$this->id/images/$this->image");
        if ($fileexists) {
            return Storage::disk('publicNas')->url("berita/$this->id/images/$this->image");
        } else {
            return Storage::disk('public')->url("no_image.png");
        }
    }
}
