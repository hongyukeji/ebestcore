<?php

namespace System\Http\Controllers\Frontend\User;

use System\Http\Controllers\Frontend\Controller;
use System\Models\Order;
use System\Models\PaymentLog;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $per_page = $request->input('per_page', config('params.pages.per_page', 15));
        $builder = Order::query()->where('user_id', $user->id);

        // Search 参数用来模糊搜索数据
        if ($request->filled('search')) {
            $search = $request->input('search');
            $like = "%{$search}%";
            $builder->where(function ($query) use ($like) {
                $query->where('order_no', 'like', $like)
                    ->orWhere('shop_name', 'like', $like)
                    ->orWhere('consignee_name', 'like', $like)
                    ->orWhere('consignee_phone', 'like', $like)
                    ->orWhere('consignee_address', 'like', $like)
                    ->orWhere('remark', 'like', $like)
                    ->orWhereHas('details', function ($query) use ($like) {
                        $query->where('product_name', 'like', $like)
                            ->orWhere('product_sku_name', 'like', $like);
                    });
            });
        }

        // 全部订单
        $orders = $builder
            ->orderBy('created_at', 'desc')
            ->paginate($per_page, ['*'], 'all')
            ->appends('order_status', 'all');

        // 未付款
        $orders_unpaid = $builder
            ->statusUnpaid()
            ->orderBy('created_at', 'desc')
            ->paginate($per_page, ['*'], 'unpaid')
            ->appends('order_status', 'unpaid');

        // 待发货
        $orders_wait_delivery = $builder
            ->statusWaitDelivery()
            ->orderBy('created_at', 'desc')
            ->paginate($per_page, ['*'], 'wait_delivery')
            ->appends('order_status', 'wait_delivery');

        // 待收货
        $orders_wait_received = $builder
            ->statusWaitReceived()
            ->orderBy('created_at', 'desc')
            ->paginate($per_page, ['*'], 'wait_received')
            ->appends('order_status', 'wait_received');

        // 已完成
        $orders_finish = $builder
            ->statusFinish()
            ->orderBy('created_at', 'desc')
            ->paginate($per_page, ['*'], 'finish')
            ->appends('order_status', 'finish');

        // 已完成待评价
        $orders_wait_comment = $builder
            ->statusWaitComment()
            ->orderBy('created_at', 'desc')
            ->paginate($per_page, ['*'], 'wait_comment')
            ->appends('order_status', 'wait_comment');

        return view('frontend::users.orders.index', compact('orders', 'orders_unpaid', 'orders_wait_delivery', 'orders_wait_received', 'orders_finish', 'orders_wait_comment'));
    }

    public function show(Order $order)
    {
        $order or abort(404);
        return view('frontend::users.orders.show', compact('order'));
    }

    public function payment(Order $order)
    {
        $user_id = auth()->id();

        // 检查订单是否存在
        if (!$order || $order->user_id != $user_id) {
            return redirect()->back()->with('error', '订单不存在');
        }

        // 检查订单是否已经支付
        if ($order->payment_at) {
            return redirect()->back()->with('error', '订单已支付');
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
        return redirect(route('frontend.payments.index', ['payment_no' => $payment_log->payment_no]));
    }

    public function confirmReceived(Order $order)
    {
        $order or abort(404);
        if ($order->user_id !== auth()->id()) {
            return redirect()->back()->with('error', '订单不存在！');
        }
        $order->update([
            'received_at' => now(),
            'status' => Order::STATUS_FINISH
        ]);

        // 订单结算事件
        event(new \System\Events\Orders\OrderSettlementEvent($order));

        return redirect()->back()->with('message', '订单确认收货成功！');
    }

    public function cancelOrder(Order $order)
    {
        $order or abort(404);
        if ($order->user_id !== auth()->id()) {
            return redirect()->back()->with('error', '订单不存在！');
        }
        $order->update([
            'closed_at' => now(),
            'status' => Order::STATUS_CANCELLED
        ]);

        return redirect()->back()->with('message', '订单取消成功！');
    }
}
