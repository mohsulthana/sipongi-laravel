<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{   
    public $incrementing = false;
    protected $table = 'visitor';
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'count',
    ];
}
