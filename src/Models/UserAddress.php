<?php

namespace System\Models;

class UserAddress extends Model
{
    /**
     * 追加到模型数组表单的访问器。
     *
     * @var array
     */
    protected $appends = [
        'address_format',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault(function ($user) {
            $user->id = 0;
            $user->name = '';
        });
    }

    public function getMobileAttribute($value)
    {
        return $value ?? ($this->phone ?? null);
    }

    public function getPhoneAttribute($value)
    {
        return $value ?? ($this->mobile ?? null);
    }

    public function setPostalCodeAttribute($value)
    {
        $this->attributes['postal_code'] = !empty($value) ? intval($value) : $value;
    }

    public function getAddressFormatAttribute()
    {
        // detailed_address
        return $this->province . $this->city . $this->district . $this->address;
    }
}
