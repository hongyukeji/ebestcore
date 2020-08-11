<?php

namespace System\Observers;

use Illuminate\Support\Carbon;
use System\Models\Order;
use System\Models\OrderDetail;
use System\Models\Product;
use System\Models\ProductSku;
use System\Services\OrderService;

class OrderObserver
{

    /**
     * 监听数据即将创建的事件。
     *
     * @param Order $order
     * @return void
     */
    public function creating(Order $order)
    {

    }

    /**
     * 监听数据创建后的事件。
     *
     * @param Order $order
     * @return void
     */
    public function created(Order $order)
    {

    }

    /**
     * 监听数据即将更新的事件。
     *
     * @param Order $order
     * @return void
     */
    public function updating(Order $order)
    {

    }

    /**
     * 监听数据更新后的事件。
     *
     * @param Order $order
     * @return void
     */
    public function updated(Order $order)
    {

    }

    /**
     * 监听数据即将保存的事件。
     *
     * @param Order $order
     * @return void
     */
    public function saving(Order $order)
    {
        // 状态
        if (is_null($order->status)) {
            $order->status = Order::STATUS_UNPAID;
        }
    }

    /**
     * 监听数据保存后的事件。
     *
     * @param Order $order
     * @return void
     */
    public function saved(Order $order)
    {
        // 订单已付款
        if (
            (empty($order->getOriginal('payment_at')) && !empty($order->payment_at)) ||
            ($order->getOriginal('status') !== Order::STATUS_PAID && $order->status == Order::STATUS_PAID)
        ) {
            // 订单已付款事件
            event(new \System\Events\Orders\OrderPaidEvent($order));
        }

        // 订单已完成: 订单原状态不是已经完成, 更改后是已完成
        if ($order->getOriginal('status') !== Order::STATUS_FINISH && $order->status == Order::STATUS_FINISH && empty($order->settlement_at)) {
            // 订单已完成事件
            event(new \System\Events\Orders\OrderFinishedEvent($order));
            // 订单结算事件
            event(new \System\Events\Orders\OrderSettlementEvent($order));
        }
    }

    /**
     * 监听数据即将删除的事件。
     *
     * @param Order $order
     * @return void
     */
    public function deleting(Order $order)
    {
        // 判断是否是彻底删除
        if (!$order->deleted_at) {
            // 删除订单对应的订单详情表数据
            OrderDetail::query()->where('order_id', $order->id)->delete();
        }
    }

    /**
     * 监听数据删除后的事件。
     *
     * @param Order $order
     * @return void
     */
    public function deleted(Order $order)
    {

    }

    /**
     * 监听数据即将从软删除状态恢复的事件。
     *
     * @param Order $order
     * @return void
     */
    public function restoring(Order $order)
    {

    }

    /**
     * 监听数据从软删除状态恢复后的事件。
     *
     * @param Order $order
     * @return void
     */
    public function restored(Order $order)
    {

    }

    /**
     * 获取到模型实例后触发
     *
     * @param $order
     * @return void
     */
    public function retrieved(Order $order)
    {
        // 自动触发确认收货: 订单已发货 && 收货时间为空
        if (!empty($order->shipped_at) && empty($order->received_at)) {
            $confirm_receipt_time = config('params.orders.confirm_receipt_ttl', 60 * 60 * 24 * 10);
            // 超出确认收货时间
            if ($order->shipped_at >= Carbon::parse($order->shipped_at)->addSeconds($confirm_receipt_time)->toDateTimeString()) {
                $order->update([
                    'received_at' => now(),
                    'status' => Order::STATUS_FINISH
                ]);
                // 订单结算事件
                event(new \System\Events\Orders\OrderSettlementEvent($order));
            }
        }

        // 判断订单付款时间是否超时, 超时后关闭订单
        $payment_time = config('params.orders.payment_ttl', 60 * 60 * 24);
        if (!$order->status_paid && $order->created_at >= Carbon::parse($order->created_at)->addSeconds($payment_time)->toDateTimeString()) {
            (new OrderService)->closeUnpaidOrder($order);
        }
    }
}
