<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Regional extends Model
{
    public $incrementing = false;
    protected $table = 'regional';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'nama_regional',
        'x',
        'y',
    ];
}
