<?php

namespace System\Models;

class ProductExtend extends Model
{
    /**
     * 不可批量赋值的属性。
     *
     * @var array
     */
    protected $guarded = [];

    public function getAfterServicesAttribute($value)
    {
        if (!empty($value)) {
            $after_services = @json_decode($value);
            if (is_array($after_services)) {
                return $after_services;
            }
        }
        return $value;
    }
}
