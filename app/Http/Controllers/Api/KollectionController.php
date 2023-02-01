<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\KollectionResource;
use App\Models\Kollection;
use Illuminate\Http\Request;

class KollectionController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->has('limit') ? $request->limit : 10;

        $collections = Kollection::published()->paginate($limit);

        return KollectionResource::collection($collections);
    }

    public function show(Kollection $collection)
    {
        activity()
            ->performedOn($collection)
            ->event('visited')
            ->log('the collection screen');

        return KollectionResource::make($collection);
    }
}
