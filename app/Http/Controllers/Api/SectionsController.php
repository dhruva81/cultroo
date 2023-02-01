<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SectionResource;
use App\Models\Section;
use Illuminate\Http\Request;

class SectionsController extends Controller
{
    public function homeScreen()
    {
        $sections = Section::query()
                ->published()
                ->get()
                ->take(25)
                ->filter(function (Section $section) {
                    return count($section->getItems()) > 0;
                });
        ;

        return SectionResource::collection($sections);
    }
}
