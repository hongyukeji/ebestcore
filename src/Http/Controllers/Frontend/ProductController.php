<?php

namespace System\Http\Controllers\Frontend;

use System\Models\Product;
use Illuminate\Http\Request;
use System\Exceptions\ProductException;
use System\Http\Resources\ProductResource;
use System\Repository\Interfaces\ProductInterface;

class ProductController extends Controller
{
    protected $product;

    public function __construct(ProductInterface $product)
    {
        $this->product = $product;
    }

    public function index(Request $request)
    {
        if (!$request->has('sort')) {
            $request->offsetSet('sort', 'id');
            $request->offsetSet('per_page', 16);
        }
        $items = $this->product->search();
        return view('frontend::products.index', compact('items'));
    }

    public function show(Product $product)
    {
        if (is_null($product) || $product->status != 1) {
            throw new ProductException('商品不存在或已下架', 404);
        }
        event(new \System\Events\Products\BrowseProductEvent($product));
        return view('frontend::products.show', compact('product'));
    }
}
