<?php

namespace System\Http\Controllers\Frontend;

use System\Models\Cart;
use System\Models\Product;
use System\Models\ProductSku;
use Illuminate\Http\Request;
use System\Services\CartService;

class CartController extends Controller
{
    public function index()
    {
        $user_id = auth()->user()->id;
        $carts = Cart::query()
            ->with(['shop', 'product'])
            ->where('user_id', $user_id)
            ->orderBy('shop_id', 'asc')
            ->get()
            ->groupBy('shop_id');
        return view('frontend::carts.index', ['items' => $carts]);
    }

    public function store(Request $request)
    {
        $cart_service = new CartService();
        return $cart_service->add();
    }

    public function update($id)
    {
        $cart = Cart::query()->where('id', $id)->where('user_id', auth()->id())->first() or abort(404);
        $cart->update(request()->all());
        return api_result(0);
    }

    public function destroy($id)
    {
        $ids = explode(',', $id);
        Cart::query()->whereIn('id', $ids)->where('user_id', auth()->id())->delete();
        if (request()->expectsJson() || request()->ajax() || request()->filled('ajax')) {
            return api_result(0);
        }
        return redirect()->back()->with('success', '购物车商品删除成功');
    }

    public function changeChecked(Request $request)
    {
        $cart = Cart::find($request->input('cart_id'));
        $cart->selected = $request->input('selected');
        $cart->save();
        return response()->json(result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
    }

    public function calcTotalPrice()
    {
        $totalPrice = 0;
        $carts = Cart::query()->where(['selected' => true, 'user_id' => auth()->user()->id])->get();
        foreach ($carts as $cart) {
            $totalPrice += $cart->number * $cart->goodsSku->price;
        }
        return response()->json(result(0, null, ['total_price' => $totalPrice]), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
    }
}
