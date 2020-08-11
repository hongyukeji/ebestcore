<?php

namespace System\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;

    public const STATUS_UNPAID = 0; // 未付款
    public const STATUS_PAID = 10;  // 已付款
    public const STATUS_WAIT_DELIVERY = 15;  // 待发货
    public const STATUS_SHIPPED = 20;  // 已发货
    public const STATUS_WAIT_RECEIVED = 25;  // 待收货
    public const STATUS_RECEIVED = 30;  // 已收货
    public const STATUS_FINISH = 100;  // 已完成
    public const STATUS_CANCELLED = 200;  // 已取消

    public const COMMENT_STATUS_INACTIVE = 0;  // 未评价
    public const COMMENT_STATUS_ACTIVE = 1; // 已评价
    public const COMMENT_STATUS_OVERDUE = 10;  // 已过期未评价

    public const ORDER_STATUS = [
        self::STATUS_CANCELLED => '已取消',
        self::STATUS_UNPAID => '未付款',
        //self::STATUS_PAID => '已付款',
        self::STATUS_WAIT_DELIVERY => '待发货',
        self::STATUS_SHIPPED => '已发货',
        self::STATUS_WAIT_RECEIVED => '待收货',
        self::STATUS_RECEIVED => '已收货',
        self::STATUS_FINISH => '已完成',
    ];

    public const REFUND_STATUS = [
        'pending' => '未退款',
        'applied' => '已申请退款',
        'processing' => '退款中',
        'success' => '退款成功',
        'failed' => '退款失败',
    ];

    public const SHIP_STATUS = [
        'pending' => '未发货',
        'delivered' => '已发货',
        'received' => '已收货',
    ];

    protected $casts = [
        'closed' => 'boolean',
        'reviewed' => 'boolean',
        //'address' => 'json',
        //'extra' => 'json',
    ];

    protected $dates = [
        'paid_at',
    ];

    /**
     * 追加到模型数组表单的访问器。
     *
     * @var array
     */
    protected $appends = [
        //'closed_status', 'status_unpaid', 'status_paid', 'status_wait_received', 'status_wait_comment',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault(function ($user) {
            $user->id = 0;
        });
    }

    /*
     * 待付款 - 订单列表
     */
    public function scopeStatusUnpaid($query)
    {
        return $query->where('status', self::STATUS_UNPAID);
    }

    /*
     * 待发货 - 订单列表
     */
    public function scopeStatusWaitDelivery($query)
    {
        return $query->where('status', self::STATUS_WAIT_DELIVERY)->orWhere('status', Order::STATUS_PAID);
    }

    /*
     * 待收货 - 订单列表
     */
    public function scopeStatusWaitReceived($query)
    {
        return $query->whereNotNull('shipped_at')->whereNull('received_at')
            ->orWhere('status', self::STATUS_WAIT_RECEIVED)
            ->orWhere('status', Order::STATUS_SHIPPED);
    }

    /*
     * 已完成 - 订单列表
     */
    public function scopeStatusFinish($query)
    {
        return $query->where('status', self::STATUS_FINISH)->orWhere('status', Order::STATUS_RECEIVED);
    }

    /*
     * 待评价 - 订单列表
     */
    public function scopeStatusWaitComment($query)
    {
        return $query->where('status', self::STATUS_FINISH)->orWhere('status', Order::STATUS_RECEIVED)->whereNull('comment_at');
    }

    /*
     * 订单是否已关闭 - 订单状态
     */
    public function getClosedStatusAttribute()
    {
        if (!empty($this->closed_at) || $this->status == self::STATUS_CANCELLED) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 是否待付款 - 订单状态
     */
    public function getStatusUnpaidAttribute()
    {
        if ($this->closed_status) {
            return false;
        }
        if ($this->status == self::STATUS_UNPAID || empty($this->status)) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 是否已付款 - 订单状态
     */
    public function getStatusPaidAttribute()
    {
        if ($this->status == self::STATUS_PAID || !empty($this->payment_at)) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 是否待收货 - 订单状态
     */
    public function getStatusWaitReceivedAttribute()
    {
        if ($this->shipped_at && empty($this->received_at)) {
            return true;
        } else if ($this->status == self::STATUS_WAIT_RECEIVED || $this->status == self::STATUS_SHIPPED) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 是否待评价 - 订单状态
     */
    public function getStatusWaitCommentAttribute()
    {
        if ($this->received_at && empty($this->comment_at) && ($this->comment_status != self::COMMENT_STATUS_OVERDUE && $this->comment_status != self::COMMENT_STATUS_ACTIVE)) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 是否开发票 invoice_status
     */
    public function getInvoiceStatusAttribute()
    {
        if (!empty($this->invoice)) {
            return true;
        } else {
            return false;
        }
    }

    /*
     * 获取支付日志表
     */
    public function paymentLog()
    {
        return $this->belongsTo(PaymentLog::class, 'payment_log_id', 'id')->withDefault(function ($paymentLog) {
            $paymentLog->id = 0;
        });
    }

    /*
     * 订单详情
     */
    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    /*
     * 订单详情
     */
    public function items()
    {
        return $this->hasMany(OrderDetail::class, 'order_id', 'id');
    }

    public function shop()
    {
        return $this->hasOne(Shop::class, 'id', 'shop_id')->withDefault(function ($shop) {
            $shop->id = 0;
            $shop->name = config('shop.self_name', '平台自营');
            $shop->support_invoice = config('shop.support_invoice', false);
        });
    }

    public function getStatusFormatAttribute()
    {
        if (isset(self::ORDER_STATUS[$this->status])) {
            return self::ORDER_STATUS[$this->status];
        } else {
            return '未知状态';
        }
    }

    public function getPaymentAtAttribute($value)
    {
        if ($value) {
            return $value;
        } elseif (isset($this->paymentLog)) {
            return $this->paymentLog->payment_at;
        }
        return null;
    }

    public function getPaymentCodeAttribute($value)
    {
        if ($value) {
            return $value;
        } elseif (isset($this->paymentLog)) {
            return $this->paymentLog->payment_code;
        }
        return null;
    }

    public function getPaymentMethodAttribute($value)
    {
        if ($value) {
            return $value;
        } elseif (isset($this->paymentLog)) {
            return $this->paymentLog->payment_method;
        }
        return null;
    }

    public function getPaymentTradeNoAttribute($value)
    {
        if ($value) {
            return $value;
        } elseif (isset($this->paymentLog)) {
            return $this->paymentLog->payment_trade_no;
        }
        return null;
    }

    public function getShippingFeeAttribute($value)
    {
        return number_format($value, 2, '.', '');
    }

    public function getTotalAmountAttribute($value)
    {
        return number_format($value, 2, '.', '');
    }

    public function getAddressAttribute($value)
    {
        //return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return json_decode($value, true);
    }

    public function getInvoiceAttribute($value)
    {
        //return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return json_decode($value, true);
    }
}
