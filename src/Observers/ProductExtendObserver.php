<?php

namespace System\Observers;

use System\Models\AfterService;
use System\Models\ProductExtend;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class ProductExtendObserver
{
    /**
     * 监听数据即将创建的事件。
     *
     * @param ProductExtend $productExtend
     * @return void
     */
    public function creating(ProductExtend $productExtend)
    {

    }

    /**
     * 监听数据创建后的事件。
     *
     * @param ProductExtend $productExtend
     * @return void
     */
    public function created(ProductExtend $productExtend)
    {
        //
    }

    /**
     * 监听数据即将更新的事件。
     *
     * @param ProductExtend $productExtend
     * @return void
     */
    public function updating(ProductExtend $productExtend)
    {

    }

    /**
     * 监听数据更新后的事件。
     *
     * @param ProductExtend $productExtend
     * @return void
     */
    public function updated(ProductExtend $productExtend)
    {

    }

    /**
     * 监听数据即将保存的事件。
     *
     * @param ProductExtend $productExtend
     * @return void
     */
    public function saving(ProductExtend $productExtend)
    {
        //
        if (is_array($productExtend->after_services)) {
            $after_services = AfterService::query()->where([
                'is_lock' => true,
                'status' => true,
            ])->orderByDesc('sort')->get();
            $productExtend->after_services = array_merge_recursive($productExtend->after_services, $after_services->pluck('id')->toArray());
        }
    }

    /**
     * 监听数据保存后的事件。
     *
     * @param ProductExtend $productExtend
     * @return void
     */
    public function saved(ProductExtend $productExtend)
    {

    }

    /**
     * 监听数据即将删除的事件。
     *
     * @param ProductExtend $productExtend
     * @return void
     */
    public function deleting(ProductExtend $productExtend)
    {

    }

    /**
     * 监听数据删除后的事件。
     *
     * @param ProductExtend $productExtend
     * @return void
     */
    public function deleted(ProductExtend $productExtend)
    {

    }

    /**
     * 监听数据即将从软删除状态恢复的事件。
     *
     * @param ProductExtend $productExtend
     * @return void
     */
    public function restoring(ProductExtend $productExtend)
    {

    }

    /**
     * 监听数据从软删除状态恢复后的事件。
     *
     * @param ProductExtend $productExtend
     * @return void
     */
    public function restored(ProductExtend $productExtend)
    {

    }
}
