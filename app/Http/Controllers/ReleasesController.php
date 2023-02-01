<?php

namespace App\Http\Controllers;

class ReleasesController extends Controller
{
    public function __invoke()
    {
        return view('releases');
    }
}
