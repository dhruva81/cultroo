<?php

namespace App\Http\Controllers;

use GrahamCampbell\Markdown\Facades\Markdown;

class DocsController extends Controller
{
    public function __invoke()
    {
        $yo = file_get_contents(base_path('docs/hello.md'));
        $data = Markdown::convert($yo)->getContent();

        return view('docs', compact('data'));
    }
}
