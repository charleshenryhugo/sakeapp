<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Set;

class SetController extends Controller
{
    public function show(Request $request)
    {
        $set1 = Set::find(1);
        return $set1;
    }
}
