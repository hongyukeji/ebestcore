<?php

namespace System\Http\Controllers\Api\V1;

use System\Http\Controllers\Api\Controller;
use System\Models\Brand;
use Illuminate\Http\Request;
use System\Http\Resources\BrandResource;
use System\Http\Resources\CategoryResource;

class BrandsController extends Controller
{
    public function index(Request $request)
    {
        $items = Brand::query()
            ->where('status', true)
            ->orderBy('sort', 'desc')
            ->get();
        return BrandResource::collection($items)->additional(api_result(0, null));
    }

    public function show($id)
    {
        $brand = Brand::query()->find($id) or abort(404);
        return api_result(0, null, new BrandResource($brand));
    }
}
