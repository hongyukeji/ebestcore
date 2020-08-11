<?php

namespace System\Http\Controllers\Helper;

use System\Models\Order;

class PrintController extends Controller
{
    public function index()
    {
        //
    }

    /*
     * 打印订单
     */
    public function order()
    {
        $order = Order::query()->first();
        return view('helper.prints.order', compact('order'));
    }

    /*
     * 打印小票订单
     */
    public function minOrder()
    {
        //
    }

}
