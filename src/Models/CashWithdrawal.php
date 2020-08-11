<?php

namespace System\Models;

class CashWithdrawal extends Model
{
    public const STATUS_WAIT = 0;
    public const STATUS_FINISH = 1;
    public const STATUS_REFUSE = 2;

    public const STATUS = [
        self::STATUS_WAIT => '待处理',
        self::STATUS_FINISH => '已处理',
        self::STATUS_REFUSE => '已拒绝',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault(function ($user) {
            $user->id = 0;
        });
    }

    public function cashWithdrawalAccount()
    {
        return $this->belongsTo(CashWithdrawalAccount::class, 'cash_withdrawal_account_id', 'id')->withDefault(function ($item) {
            $item->id = 0;
        });
    }

    public function account()
    {
        return $this->hasOne(CashWithdrawalAccount::class, 'cash_withdrawal_account_id', 'id')->withDefault(function ($account) {
            $account->id = 0;
        });
    }

    public function getStatusFormatAttribute()
    {
        if (isset(self::STATUS[$this->status])) {
            return self::STATUS[$this->status];
        } else {
            return '未知';
        }
    }
}
