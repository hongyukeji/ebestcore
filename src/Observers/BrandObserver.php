<?php

namespace System\Observers;

use System\Models\Brand;
use Illuminate\Support\Facades\Log;

class BrandObserver
{

    /**
     * 监听数据即将创建的事件。
     *
     * @param Brand $brand
     * @return void
     */
    public function creating(Brand $brand)
    {

    }

    /**
     * 监听数据创建后的事件。
     *
     * @param Brand $brand
     * @return void
     */
    public function created(Brand $brand)
    {

    }

    /**
     * 监听数据即将更新的事件。
     *
     * @param Brand $brand
     * @return void
     */
    public function updating(Brand $brand)
    {

    }

    /**
     * 监听数据更新后的事件。
     *
     * @param Brand $brand
     * @return void
     */
    public function updated(Brand $brand)
    {

    }

    /**
     * 监听数据即将保存的事件。
     *
     * @param Brand $brand
     * @return void
     */
    public function saving(Brand $brand)
    {
        // 品牌首字母
        if (is_null($brand->letter) || $brand->isDirty('name')) {
            try {
                $str = pinyin_abbr($brand->name, PINYIN_DEFAULT);
                if (empty($str)) {
                    $str = $brand->name;
                }
                $letter = strtoupper(substr($str, 0, 1));
                $brand->letter = $letter;
            } catch (\Exception $e) {
                Log::warning('[BrandObserver] ' . $e->getMessage());
            }
        }

    }

    /**
     * 监听数据保存后的事件。
     *
     * @param Brand $brand
     * @return void
     */
    public function saved(Brand $brand)
    {

    }

    /**
     * 监听数据即将删除的事件。
     *
     * @param Brand $brand
     * @return void
     */
    public function deleting(Brand $brand)
    {

    }

    /**
     * 监听数据删除后的事件。
     *
     * @param Brand $brand
     * @return void
     */
    public function deleted(Brand $brand)
    {

    }

    /**
     * 监听数据即将从软删除状态恢复的事件。
     *
     * @param Brand $brand
     * @return void
     */
    public function restoring(Brand $brand)
    {

    }

    /**
     * 监听数据从软删除状态恢复后的事件。
     *
     * @param Brand $brand
     * @return void
     */
    public function restored(Brand $brand)
    {

    }
}
