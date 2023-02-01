<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SupportCategoryResource;
use App\Models\SupportCategory;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function supportCategories()
    {
        return SupportCategoryResource::collection(SupportCategory::all());
    }

    public function showSupportCategories(SupportCategory $supportCategory)
    {
        $supportCategory->load('faqs');
        return new SupportCategoryResource($supportCategory);
    }
}
