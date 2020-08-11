<?php

namespace System\Presenters;

use System\Models\Cart;
use Illuminate\Support\Facades\Auth;

class CartPresenter
{
    public function all()
    {
        if (Auth::check()) {
            // 用户已经登录了...
            $user_id = Auth::id();
            $carts = Cart::query()
                ->with(['shop', 'product'])
                ->where('user_id', $user_id)
                ->orderBy('updated_at', 'desc') // ->orderBy('shop_id', 'asc')
                ->get();    // ->groupBy('shop_id')
            return $carts;
        } else {
            return null;
        }
    }

    public function info()
    {
        $info = [
            'total_number' => 0,
            'total_amount' => 0.00,
            'total_freight' => 0,
        ];

        if (Auth::check()) {
            // 用户已经登录了...
            $user_id = Auth::id();
            $carts = Cart::query()
                ->with(['shop', 'product'])
                ->where('user_id', $user_id)
                ->orderBy('shop_id', 'asc')
                ->get();    // ->groupBy('shop_id')

            // 购物车总金额
            $total_price = 0;
            foreach ($carts as $cart) {
                $price = $cart->productSku->price > 0 ? $cart->productSku->price : $cart->product->price;
                $total_price += number_format($price * $cart->number, 2, '.', '');
            }
            $info['total_number'] = $carts->sum('number');
            $info['total_amount'] = number_format($total_price, 2, '.', '');
        }
        return collect($info);
    }
}
