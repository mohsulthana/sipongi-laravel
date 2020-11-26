<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    protected $primaryKey = 'account';
    public $incrementing = false;
    protected $table = 'settings';
    public $timestamps = false;

    protected $fillable = [
        'account',
        'position',
        'running',
        'config'
    ];

    protected $casts = [
        'config' => 'object',
    ];
}
