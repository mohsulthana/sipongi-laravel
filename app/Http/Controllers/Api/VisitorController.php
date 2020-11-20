<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Visitor;
use Illuminate\Support\Str;

class VisitorController extends Controller
{
    public function index(Request $request)
    {
        $visitor = Visitor::first();
        if($visitor)
        {
            return ['visitor' => number_format($visitor->count , 0, ',', '.')];
        }

        return ['visitor' => 849269];
    }

    public function count(Request $request)
    {
        $visitor = Visitor::first();
        if($visitor)
        {
            $visitor->count = $visitor->count + 1;
            $visitor->save();
        }else{
            Visitor::create([
                'id' => Str::orderedUuid(),
                'count' => 849269,
            ]);
        }

        return ['status' => true];
    }
}