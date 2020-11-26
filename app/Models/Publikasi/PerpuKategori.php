<?php

namespace App\Models\Publikasi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class PerpuKategori extends Model
{
    use SoftDeletes;

    public $incrementing = false;
    protected $table = 'pub_perpu_kategori';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'name',
        'slug',
        'created_at',
    ];

    protected $dates = ['deleted_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Str::orderedUuid();

            $model->slug = Str::slug(Str::lower($model->name));

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
            if ($model->name != $model->getOriginal('name')) {
                $model->slug = Str::slug(Str::lower($model->name));

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

    public function perpu()
    {
        return $this->hasMany(Perpu::class, 'kategori_id', 'id')->orderBy('created_at', 'desc');
    }
}
