<?php

namespace System\Services;

use Illuminate\Support\Facades\Cache;

class CacheService extends Service
{
    public const CACHE_GROUP_SYSTEM = 'cache_group_system_';

    /*
     * 存入缓存组
     */
    public function foreverGroup($cache_group_key, $cache_keys)
    {
        if (!is_array($cache_keys)) {
            $cache_keys = [$cache_keys];
        }
        $cache_group_val = array_merge_recursive((array)Cache::get($cache_group_key, []), $cache_keys);
        Cache::forever($cache_group_key, $cache_group_val);
    }

    /*
     * 清除缓存组
     */
    public function forgetGroup($cache_group_key)
    {
        foreach ((array)Cache::get($cache_group_key, []) as $cache_key) {
            Cache::forget($cache_key);
        }
    }
}
