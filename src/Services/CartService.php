<?php

namespace System\Services;

use System\Models\Cart;
use System\Models\Product;
use System\Models\ProductSku;
use Illuminate\Http\Request;

class CartService extends Service
{
    public function add()
    {
        $request = request();
        $user_id = auth()->id();
        $product_id = $request->input('product_id');
        $product_sku_id = $request->input('product_sku_id');
        $number = $request->input('number', 1);
        $is_selected = $request->input('is_selected', false);

        // 判断商品
        $orm_product = Product::query()->find($product_id);
        if (!$orm_product) {
            return api_result(1, '商品不存在');
        }
        // 判断商品状态
        if ($orm_product->status != 1) {
            return api_result(1, '商品' . $orm_product->status_format);
        }
        // 判断商品库存是否足够-无sku
        if ($orm_product->stock < $number) {
            return api_result(1, '商品库存不足');
        }

        // 判断商品是否存在sku
        if ($orm_product->is_sku) {
            // 判断商品规格是否存在
            $orm_product_sku = ProductSku::query()
                ->where('product_id', $orm_product->id)
                ->where('id', $product_sku_id)
                ->first();
            if (!$orm_product_sku) {
                return api_result(501, '商品规格不存在');
            }
            // 判断商品sku库存是否足够
            if ($orm_product_sku) {
                if ($orm_product_sku->stock < $number) {
                    return api_result(1, '商品SKU库存不足');
                }
            } else {
                return api_result(1, '商品SKU不存在');
            }
        }

        // 加入购物车
        $cart_query = [
            'user_id' => $user_id,
            'product_id' => $product_id,
        ];
        if ($product_sku_id) {
            $cart_query['product_sku_id'] = $product_sku_id;
        }

        $orm_cart = Cart::updateOrCreate($cart_query, [
            //'number' => $number,
            'shop_id' => $orm_product->shop_id,
            'is_selected' => $is_selected,
        ]);
        $orm_cart->increment('number', $number);

        return api_result(0, '商品加入购物车成功', $orm_cart);
    }
}
