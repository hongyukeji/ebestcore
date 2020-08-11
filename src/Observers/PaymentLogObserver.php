<?php

namespace System\Observers;

use System\Models\PaymentLog;
use System\Services\OrderService;
use System\Services\UserAccountService;

class PaymentLogObserver
{

    /**
     * 监听数据即将创建的事件。
     *
     * @param PaymentLog $paymentLog
     * @return void
     */
    public function creating(PaymentLog $paymentLog)
    {

    }

    /**
     * 监听数据创建后的事件。
     *
     * @param PaymentLog $paymentLog
     * @return void
     */
    public function created(PaymentLog $paymentLog)
    {

    }

    /**
     * 监听数据即将更新的事件。
     *
     * @param PaymentLog $paymentLog
     * @return void
     */
    public function updating(PaymentLog $paymentLog)
    {

    }

    /**
     * 监听数据更新后的事件。
     *
     * @param PaymentLog $paymentLog
     * @return void
     */
    public function updated(PaymentLog $paymentLog)
    {

    }

    /**
     * 监听数据即将保存的事件。
     *
     * @param PaymentLog $paymentLog
     * @return void
     */
    public function saving(PaymentLog $paymentLog)
    {

    }

    /**
     * 监听数据保存后的事件。
     *
     * @param PaymentLog $paymentLog
     * @return void
     */
    public function saved(PaymentLog $paymentLog)
    {
        if ($paymentLog->getOriginal('status') !== $paymentLog->status && $paymentLog->status) {

            switch ($paymentLog->payment_type) {
                case PaymentLog::PAYMENT_TYPE_ORDER:
                    // 订单支付
                    $order_service = new OrderService();
                    $order_service->updatePaymentStatus($paymentLog);
                    break;
                case PaymentLog::PAYMENT_TYPE_BALANCE:
                    // 余额充值
                    $user_account_service = new UserAccountService();
                    $user_account_service->rechargeBalance($paymentLog);
                    // TODO: 用户余额增加
                    break;
                default:
                    $order_service = new OrderService();
                    $order_service->updatePaymentStatus($paymentLog);
            }
        }
    }

    /**
     * 监听数据即将删除的事件。
     *
     * @param PaymentLog $paymentLog
     * @return void
     */
    public function deleting(PaymentLog $paymentLog)
    {

    }

    /**
     * 监听数据删除后的事件。
     *
     * @param PaymentLog $paymentLog
     * @return void
     */
    public function deleted(PaymentLog $paymentLog)
    {

    }

    /**
     * 监听数据即将从软删除状态恢复的事件。
     *
     * @param PaymentLog $paymentLog
     * @return void
     */
    public function restoring(PaymentLog $paymentLog)
    {

    }

    /**
     * 监听数据从软删除状态恢复后的事件。
     *
     * @param PaymentLog $paymentLog
     * @return void
     */
    public function restored(PaymentLog $paymentLog)
    {

    }
}
