<?php

namespace App\Http\Controllers\Api\Publikasi;

use App\Http\Controllers\Controller;
use App\Models\Publikasi\PerpuKategori;
use Illuminate\Http\Request;

class PerpuKategoriController extends Controller
{
    public function getAll()
    {
        $lists = PerpuKategori::query()->orderBy('created_at', 'desc')->orderBy('name')->get();
        return response()->json($lists);
    }
}
