<?php

namespace System\Models;

class CashWithdrawalAccount extends Model
{
    public const ACCOUNT_TYPE_ALIPAY = 1;
    public const ACCOUNT_TYPE_WECHAT = 2;
    public const ACCOUNT_TYPE_BANK_CARD = 3;

    public const ACCOUNT_TYPE = [
        self::ACCOUNT_TYPE_ALIPAY => '支付宝',
        self::ACCOUNT_TYPE_WECHAT => '微信',
        self::ACCOUNT_TYPE_BANK_CARD => '银行卡',
    ];

    protected static $imageFields = [
        'qr_code' => [
            'width' => 200,
            'height' => 200,
            'path' => 'uploads/images/users/qr_codes',
        ],
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault(function ($user) {
            $user->id = 0;
        });
    }

    public function getAccountTypeFormatAttribute()
    {
        if (isset(self::ACCOUNT_TYPE[$this->account_type])) {
            return self::ACCOUNT_TYPE[$this->account_type];
        } else {
            return '未知';
        }
    }
}
