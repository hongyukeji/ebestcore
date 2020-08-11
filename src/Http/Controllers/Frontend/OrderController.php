<?php

namespace System\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use System\Services\OrderService;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $order_service = new OrderService();
        $result = $order_service->getCartOrderInfo($request->input('carts'));
        if (!$result->verify()) {
            return redirect(route('frontend.carts.index'))->with('message', $result->message);
        }
        $order = $result->data;
        return view('frontend::orders.index', [
            'order' => $order,
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
        return redirect(route('frontend.payments.index', ['payment_no' => $payment_log->payment_no]));
    }
}
