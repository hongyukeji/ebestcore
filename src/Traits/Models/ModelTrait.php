<?php

namespace System\Traits\Models;

trait ModelTrait
{
    /**
     * 根据id字段降序排列
     *
     * @param $query
     * @return mixed
     */
    public function scopeRecent($query)
    {
        return $query->orderBy('id', 'desc');
    }

    /**
     * 根据sort排序
     *
     * @param $query
     * @return mixed
     */
    public function scopeSorted($query)
    {
        return $query->orderBy(config('params.models.sort_key', 'sort'), config('params.models.sort_mode', 'desc'));
    }

    /**
     * 获取status状态为真的数据
     *
     * @param $query
     * @return mixed
     */
    public function scopeActive($query)
    {
        return $query->where('status', config('params.models.status_active', true));
    }
}
