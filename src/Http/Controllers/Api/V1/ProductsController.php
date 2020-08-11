<?php

namespace System\Http\Controllers\Api\V1;

use System\Http\Controllers\Api\Controller;
use System\Http\Resources\ProductResource;
use System\Models\Product;
use System\Repository\Interfaces\ProductInterface;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    protected $product;

    public function __construct(ProductInterface $product)
    {
        $this->product = $product;
    }

    public function index(Request $request)
    {
        $products = $this->product->search();
        return ProductResource::collection($products)->additional(api_result(0, null));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        $product = $this->product->findOne($id) or abort(404);
        return api_result(0, null, new ProductResource($product));
    }

    public function edit(Product $product)
    {
        //
    }

    public function update(Request $request, Product $product)
    {
        //
    }

    public function destroy(Product $product)
    {
        //
    }
}
