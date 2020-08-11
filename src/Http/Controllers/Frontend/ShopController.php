<?php

namespace System\Http\Controllers\Frontend;

use System\Models\Product;
use System\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        return view('frontend::shops.index');
    }

    public function show(Shop $shop)
    {
        $shop or abort(404);
        $shop_all_goods = Product::query()
            ->where([
                'shop_id' => $shop->id,
                'status' => 1
            ])
            ->orderBy('sort', 'desc')
            ->get();
        $shop_best_goods = $shop->getQueryProducts('is_best', 10);
        $shop_hot_goods = $shop->getQueryProducts('is_hot', 10);
        $shop_new_goods = $shop->getQueryProducts('is_new', 10);

        return view('frontend::shops.show', [
            'shop' => $shop,
            'shop_all_goods' => $shop_all_goods,
            'shop_best_goods' => $shop_best_goods,
            'shop_hot_goods' => $shop_hot_goods,
            'shop_new_goods' => $shop_new_goods,
        ]);
    }
}
