<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Spatie\Permission;

class PermissionController extends Controller
{
    public function count()
    {
        $perm = Permission::count();
        return response()->json($perm);
    }
}
