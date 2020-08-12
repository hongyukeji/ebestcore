<?php

namespace System\Services;

use System\Models\PaymentLog;

class PaymentLogService
{
    public function checkStatus($payment_no)
    {
        $payment_log = PaymentLog::query()->where('payment_no', $payment_no)->first();
        if (!$payment_log) {
            return return_result()->failed('支付记录不存在');
        }
        if ($payment_log->status == PaymentLog::STATUS_PAID) {
            return return_result()->failed('订单已支付');
        }

        // 检查支付日志中, 是否有已经被支付的订单
        if ($payment_log->orders) {
            foreach ($payment_log->orders as $order) {
                if ($order->status_paid) {
                    return return_result()->failed('部分订单已经被支付, 请重新发起支付请求');
                }
            }
        }

        return return_result()->success($payment_log);
    }
}
