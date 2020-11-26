<?php

namespace App\Models\Publikasi;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Perpu extends Model
{
    use SoftDeletes;

    public $incrementing = false;
    protected $table = 'pub_perpu';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'kategori_id',
        'slug',
        'nomor',
        'title',
        'tipe',
        'file',
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

    public function kategori()
    {
        return $this->belongsTo(PerpuKategori::class, 'kategori_id', 'id')->withTrashed();
    }

    public function getFileUrlAttribute()
    {
        if ($this->tipe === 'file') {
            $fileexists = Storage::disk('publicNas')->exists("perpu/$this->id/file/$this->file");
            if ($fileexists) {
                return Storage::disk('publicNas')->url("perpu/$this->id/file/$this->file");
            } else {
                return null;
            }
        } else {
            return $this->file;
        }
    }

    public function getCheckTipeAttribute()
    {
        if ($this->tipe === 'file') {
            return $this->tipe;
        } else {
            return 'url';
        }
    }
}
