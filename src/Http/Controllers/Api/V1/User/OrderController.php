<?php

namespace System\Http\Controllers\Api\V1\User;

use System\Http\Controllers\Api\Controller;
use System\Models\Order;
use System\Models\PaymentLog;
use Illuminate\Http\Request;
use System\Http\Resources\OrderResource;
use System\Repository\Interfaces\OrderInterface;

class OrderController extends Controller
{
    protected $order;

    public function __construct(OrderInterface $order)
    {
        $this->order = $order;
    }

    public function index(Request $request)
    {
        $request->offsetSet('user_id', auth()->id());
        // 全部订单
        $orders = $this->order->search();
        return OrderResource::collection($orders)->additional(api_result(0, null));
    }

    public function show($id)
    {
        $order = Order::query()->where('user_id', auth()->id())->where('id', $id)->first() or abort(404);
        return api_result(0, null, new OrderResource($order));
    }

    public function payment(Request $request)
    {
        $order_id = $request->input('order_id');
        $user_id = auth()->id();
        $order = Order::query()->where('user_id', auth()->id())->where('id', $order_id)->first() or abort(404);
        // 检查订单是否存在
        if (!$order || $order->user_id != $user_id) {
            return api_result(1, '订单不存在');
        }

        // 检查订单是否已经支付
        if ($order->payment_at) {
            return api_result(1, '订单已支付');
        }

        // 检查是否存在支付记录
        if ($order->paymentLog && $order->paymentLog->status == PaymentLog::STATUS_UNPAID && $order->paymentLog->total_amount == $order->total_amount) {
            $payment_log = $order->paymentLog;
        } else {
            $payment_log = PaymentLog::create([
                'payment_no' => create_order_no(),
                'payment_type' => PaymentLog::PAYMENT_TYPE_ORDER,
                'total_amount' => $order->total_amount,
                'status' => PaymentLog::STATUS_UNPAID,
            ]);
        }

        // 跳转至订单支付页面
        return api_result(0, null, $payment_log);
    }

    public function confirmReceived(Request $request)
    {
        $order_id = $request->input('order_id');
        $order = Order::query()->where('user_id', auth()->id())->where('id', $order_id)->first() or abort(404);
        if ($order->user_id !== auth()->id()) {
            return api_result(1, '订单不存在');
        }
        $order->update([
            'received_at' => now(),
            'status' => Order::STATUS_FINISH
        ]);

        // 订单结算事件
        event(new \System\Events\Orders\OrderSettlementEvent($order));
        return api_result(0, '订单确认收货成功');
    }
}
