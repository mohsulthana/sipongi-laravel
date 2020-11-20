<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Http\Resources\Module\ModuleResources;

class ModuleController extends Controller
{
    public function getModulesPermissions()
    {
        $module = Module::orderBy('display_name')
            ->with(['permissions'])
            ->get();

        return new ModuleResources($module);
    }
}
