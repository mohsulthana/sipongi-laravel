<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Daops extends Model
{
    use SoftDeletes;

    public $incrementing = false;
    protected $table = 'daops';
    protected $keyType = 'string';

    protected $fillable = [
        'kode_daops',
        'nama_daops',
        'provinsi_id',
        'alamat',
        'telepon',
        'latitude',
        'longitude',
        'jumlah_regu',
        'sapras',
        'wilayah_kerja',
        'satuan_kerja',
        'no_sk',
        'tgl_sk',
        'jumlah_gajah',
        'jumlah_pawang',
        'catatan',
    ];

    protected $dates = ['deleted_at'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = Str::orderedUuid();
        });
    }
}
