<?php

namespace System\Http\Controllers\Api\V1;

use System\Models\Brand;
use System\Models\Shop;
use Illuminate\Http\Request;
use System\Http\Resources\BrandResource;
use System\Http\Resources\ProductResource;
use System\Http\Resources\ShopResource;
use System\Repository\Interfaces\ShopInterface;

class ShopsController extends Controller
{
    protected $shop;

    public function __construct(ShopInterface $shop)
    {
        $this->shop = $shop;
    }

    public function index(Request $request)
    {
        $shops = $this->shop->search();
        return ShopResource::collection($shops)->additional(api_result(0, null));
    }

    public function show($id)
    {
        $shop = $this->shop->findOne($id) or abort(404);
        return api_result(0, null, new ShopResource($shop));
    }
}
