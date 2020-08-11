<?php

namespace System\Http\Controllers\Mobile;

use System\Models\UserAddress;
use Illuminate\Http\Request;
use System\Services\OrderService;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $order_service = new OrderService();
        $result = $order_service->getCartOrderInfo($request->input('carts'));
        if (!$result->verify()) {
            return redirect(route('mobile.carts.index'))->with('message', $result->message);
        }
        $order = $result->data;

        // 判断是否存在默认收货地址
        $user_address = null;
        $default_address = UserAddress::query()->where([
            'user_id' => auth()->user()->id,
            'is_default' => true
        ])->first();
        if ($default_address) {
            $user_address = $default_address;
        } else {
            $user_address = UserAddress::query()->where(['user_id' => auth()->user()->id])->orderBy('updated_at', 'desc')->first();
        }
        $address_list = UserAddress::query()->where(['user_id' => auth()->user()->id])->orderBy('updated_at', 'desc')->get();

        return view('mobile::orders.index', [
            'order' => $order,
            'user_address' => $user_address,
            'address_list' => $address_list,
        ]);
    }

    public function store(Request $request)
    {
        $order_service = new OrderService();
        $payment_info = $order_service->generateCartOrder();
        if (!$payment_info->verify()) {
            return redirect()->back()->with('message', $payment_info->message);
        }
        $payment_log = $payment_info->data;
        // 跳转至订单支付页面
        return redirect(route('mobile.payments.index', ['payment_no' => $payment_log->payment_no]));
    }
}
