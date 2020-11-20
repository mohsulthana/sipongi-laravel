<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Handler as Exception;
use App\Http\Controllers\Controller;
use App\Models\Regional;
use Illuminate\Http\Request;

class RegionalController extends Controller
{
    public function getAll()
    {
        $regional = Regional::query()->orderBy('nama_regional')->get();
        return response()->json($regional);
    }
}
