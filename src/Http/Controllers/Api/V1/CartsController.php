<?php

namespace System\Http\Controllers\Api\V1;

use System\Http\Controllers\Api\Controller;
use System\Models\Cart;
use System\Models\Product;
use Illuminate\Http\Request;
use System\Http\Requests\CartRequest;
use System\Http\Resources\CartResource;
use System\Services\CartService;

class CartsController extends Controller
{
    public function index(Request $request)
    {
        // 是否按照店铺分组
        $group = $request->input('group', false);
        $user_id = auth()->user()->id;
        $carts = Cart::query()
            ->with(['shop', 'product'])
            ->where('user_id', $user_id)
            ->orderBy('shop_id', 'asc')
            ->get();
        if ($group) {
            $carts->groupBy('shop_id');
        }
        $items = $carts;
        return CartResource::collection($items)->additional(api_result(0, null));
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
        return api_result(0);
    }
}
