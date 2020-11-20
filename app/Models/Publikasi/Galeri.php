<?php

namespace App\Models\Publikasi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Galeri extends Model
{
    use SoftDeletes;

    public $incrementing = false;
    protected $table = 'pub_galeri';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'slug',
        'title',
        'tipe',
    ];

    protected $dates = ['deleted_at'];
    protected static $relations_to_cascade = ['details'];

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

        static::deleting(function ($model) {
            foreach (static::$relations_to_cascade as $relation) {
                $model->{$relation}()->delete();
            }
        });
    }

    public function details()
    {
        return $this->hasMany(GaleriDetail::class, 'galeri_id', 'id')->orderBy('created_at', 'desc');
    }

    public function details_publish()
    {
        return $this->hasMany(GaleriDetail::class, 'galeri_id', 'id')->where('publish', true)->orderBy('created_at', 'desc');
    }

    public function detail()
    {
        return $this->hasOne(GaleriDetail::class, 'galeri_id', 'id')->where('publish', true)->orderBy('created_at', 'desc');
    }
}
