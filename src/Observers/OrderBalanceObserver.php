<?php

namespace System\Observers;

use System\Models\OrderBalance;
use System\Models\UserAccount;

class OrderBalanceObserver
{

    /**
     * 监听数据即将创建的事件。
     *
     * @param OrderBalance $orderBalance
     * @return void
     */
    public function creating(OrderBalance $orderBalance)
    {

    }

    /**
     * 监听数据创建后的事件。
     *
     * @param OrderBalance $orderBalance
     * @return void
     */
    public function created(OrderBalance $orderBalance)
    {

    }

    /**
     * 监听数据即将更新的事件。
     *
     * @param OrderBalance $orderBalance
     * @return void
     */
    public function updating(OrderBalance $orderBalance)
    {

    }

    /**
     * 监听数据更新后的事件。
     *
     * @param OrderBalance $orderBalance
     * @return void
     */
    public function updated(OrderBalance $orderBalance)
    {

    }

    /**
     * 监听数据即将保存的事件。
     *
     * @param OrderBalance $orderBalance
     * @return void
     */
    public function saving(OrderBalance $orderBalance)
    {

    }

    /**
     * 监听数据保存后的事件。
     *
     * @param OrderBalance $orderBalance
     * @return void
     */
    public function saved(OrderBalance $orderBalance)
    {
        // 判断订单状态是否为已付款, 且余额到账时间为空 - 增加用户余额
        if ($orderBalance->status == OrderBalance::STATUS_PAID && empty($orderBalance->finished_at)) {
            $user_account = UserAccount::query()->find($orderBalance->user_id);
            if ($user_account) {
                // 增加余额
                $user_account->increment('money', $orderBalance->total_amount);
                // 更新订单完成时间
                $orderBalance->finished_at = now();
                $orderBalance->save();
            }
        }
    }

    /**
     * 监听数据即将删除的事件。
     *
     * @param OrderBalance $orderBalance
     * @return void
     */
    public function deleting(OrderBalance $orderBalance)
    {

    }

    /**
     * 监听数据删除后的事件。
     *
     * @param OrderBalance $orderBalance
     * @return void
     */
    public function deleted(OrderBalance $orderBalance)
    {

    }

    /**
     * 监听数据即将从软删除状态恢复的事件。
     *
     * @param OrderBalance $orderBalance
     * @return void
     */
    public function restoring(OrderBalance $orderBalance)
    {

    }

    /**
     * 监听数据从软删除状态恢复后的事件。
     *
     * @param OrderBalance $orderBalance
     * @return void
     */
    public function restored(OrderBalance $orderBalance)
    {

    }
}
