<?php

namespace System\Models;

class PaymentLog extends Model
{
    public const STATUS_UNPAID = 0; // 未付款
    public const STATUS_PAID = 1;  // 已付款

    public const STATUS = [
        self::STATUS_UNPAID => '未付款',
        self::STATUS_PAID => '已付款',
    ];

    public const PAYMENT_TYPE_DEFAULT = 0; // 默认
    public const PAYMENT_TYPE_ORDER = 1; // 订单支付
    public const PAYMENT_TYPE_BALANCE = 2; // 余额充值

    public const PAYMENT_TYPE = [
        self::PAYMENT_TYPE_DEFAULT => '默认',
        self::PAYMENT_TYPE_ORDER => '订单支付',
        self::PAYMENT_TYPE_BALANCE => '余额充值',
    ];

    /*
     * 获取所有订单
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /*
     * 获取余额订单
     */
    public function orderBalances()
    {
        return $this->hasMany(OrderBalance::class);
    }
}
