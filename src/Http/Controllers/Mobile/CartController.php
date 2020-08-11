<?php

namespace System\Http\Controllers\Mobile;

use System\Models\Cart;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            $user_id = auth()->user()->id;
            $items = Cart::query()
                ->with(['shop', 'product'])
                ->where('user_id', $user_id)
                ->orderBy('shop_id', 'asc')
                ->get()
                ->groupBy('shop_id');
        } else {
            $items = [];
        }
        return view('mobile::carts.index', compact('items'));
    }
}
