<?php

namespace System\Librarys\Payment\Gateways;

use System\Models\Order;
use System\Models\PaymentLog;
use System\Librarys\Payment\Abstracts\GatewayAbstract;

class Cash extends GatewayAbstract
{

    /**
     * 电脑端支付
     *
     * @return mixed
     */
    public function web()
    {
        /*return redirect(route('frontend.payment.return.index', [
            strtolower(class_basename(__CLASS__)),
            'payment_no'=> $this->params['order_no'] ?? '',
        ]));*/
        $gateway = strtolower(class_basename(__CLASS__));
        $request = request();
        $payment_no = $request->input('payment_no');
        $payment_log = PaymentLog::query()->where('payment_no', $payment_no)->first();
        if (!$payment_log) {
            return redirect()->back()->with('error', '支付记录不存在');
        }
        // 更新支付日志
        $payment_log->update([
            'payment_method' => config("payments.gateways.{$gateway}.name"),
            'payment_code' => $gateway,
            'payment_trade_no' => uuid(),
            'status' => PaymentLog::STATUS_PAID,
        ]);
        Order::query()->where('payment_log_id', $payment_log->id)->update([
            'status' => Order::STATUS_WAIT_DELIVERY,
        ]);
        return redirect(route_url('frontend.payments.result', ['payment_no' => $payment_log->payment_no]));
    }

    /**
     * 移动端支付
     *
     * @return mixed
     */
    public function wap()
    {
        $gateway = strtolower(class_basename(__CLASS__));
        $request = request();
        $payment_no = $request->input('payment_no');
        $payment_log = PaymentLog::query()->where('payment_no', $payment_no)->first();
        if (!$payment_log) {
            return redirect()->back()->with('error', '支付记录不存在');
        }
        // 更新支付日志
        $payment_log->update([
            'payment_method' => config("payments.gateways.{$gateway}.name"),
            'payment_code' => $gateway,
            'payment_trade_no' => uuid(),
            'status' => PaymentLog::STATUS_PAID,
        ]);
        Order::query()->where('payment_log_id', $payment_log->id)->update([
            'status' => Order::STATUS_WAIT_DELIVERY,
        ]);
        return redirect(route_url('mobile.payments.result', ['payment_no' => $payment_log->payment_no]));
    }

    /**
     * App端支付
     *
     * @return mixed
     */
    public function app()
    {
        // TODO: Implement app() method.
    }

    /**
     * 验证签名
     *
     * @return mixed
     */
    public function verify()
    {
        // TODO: Implement verify() method.
    }
}
