<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\Handler as Exception;
use App\Http\Controllers\Controller;
use App\Models\Provinsi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProvinsiController extends Controller
{
    public function getAll()
    {
        $prov = Provinsi::query()->select(['id', 'nama_provinsi'])->orderBy('nama_provinsi')->get();
        return response()->json($prov);
    }

    public function getByRegional($reg_id)
    {
        $prov = Provinsi::query()
            ->select(['id', 'nama_provinsi'])
            ->where('regional_id', $reg_id)
            ->orderBy('nama_provinsi')
            ->get();
        return response()->json($prov);
    }
}
