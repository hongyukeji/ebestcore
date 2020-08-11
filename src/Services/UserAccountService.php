<?php

namespace System\Services;

use System\Models\OrderBalance;
use System\Models\PaymentLog;

class UserAccountService extends Service
{
    /*
     * 创建充值余额订单
     */
    public function createRechargeBalanceOrder($money)
    {
        $payment_log = PaymentLog::create([
            'payment_no' => create_order_no(),
            'payment_type' => PaymentLog::PAYMENT_TYPE_BALANCE,
            'total_amount' => $money,
            'status' => PaymentLog::STATUS_UNPAID,
        ]);

        $user = auth()->user();
        $order_no = build_number_no();
        $device = get_client_os();
        $order_balance = OrderBalance::create([
            'order_no' => $order_no,
            'order_source' => $device,
            'payment_log_id' => $payment_log->id,
            'user_id' => $user->id,
            'total_amount' => $money,
            'status' => OrderBalance::STATUS_UNPAID,
        ]);

        return $payment_log;
    }

    /*
     * 余额充值
     */
    public function rechargeBalance(PaymentLog $payment_log)
    {
        if (isset($payment_log->orderBalances) && count($payment_log->orderBalances)) {
            // 判断订单日志总金额和关联订单总金额是否相等
            if ($payment_log->total_amount == $payment_log->orderBalances->sum('total_amount')) {
                // 相等 - 循环更改订单状态为已支付
                foreach ($payment_log->orderBalances as $order) {
                    $order->update([
                        'payment_at' => now(),
                        'status' => OrderBalance::STATUS_PAID
                    ]);
                }
                return true;
            }
        }

        // 不相等 - 查找符合 订单日志总金额 的订单
        return false;
    }
}
