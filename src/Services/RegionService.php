<?php

namespace System\Services;

use System\Models\Region;

class RegionService extends Service
{
    /*
     * 根据中文名称查询省份
     */
    public function getProvince($name, $parent_id = 0)
    {
        return Region::query()
            ->where('name', 'like', "%{$name}%")
            ->where('level', 1)
            ->where('parent_id', $parent_id)
            ->first();
    }

    /*
     * 根据中文名称查询城市
     */
    public function getCity($name, $parent_id = null)
    {
        return Region::query()
            ->where('name', 'like', "%{$name}%")
            ->where('level', 2)
            ->where(function ($query) use ($parent_id) {
                if ($parent_id) {
                    $query->where('parent_id', $parent_id);
                }
            })
            ->first();
    }

    /*
     * 根据中文名称查询区域
     */
    public function getDistrict($name, $parent_id = null)
    {
        return Region::query()
            ->where('name', 'like', "%{$name}%")
            ->where('level', 3)
            ->where(function ($query) use ($parent_id) {
                if ($parent_id) {
                    $query->where('parent_id', $parent_id);
                }
            })
            ->first();
    }
}
