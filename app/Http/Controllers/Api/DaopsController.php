<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Daops\GeoListResources;
use App\Models\Daops;
use Illuminate\Http\Request;

class DaopsController extends Controller
{
    public function getMarkerAll()
    {
        $lists = Daops::all();

        return new GeoListResources($lists);
    }
}
