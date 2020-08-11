<?php

namespace System\Observers;

use System\Models\ProductSku;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class ProductSkuObserver
{
    /**
     * 监听数据即将创建的事件。
     *
     * @param ProductSku $productSku
     * @return void
     */
    public function creating(ProductSku $productSku)
    {

    }

    /**
     * 监听数据创建后的事件。
     *
     * @param ProductSku $productSku
     * @return void
     */
    public function created(ProductSku $productSku)
    {

    }

    /**
     * 监听数据即将更新的事件。
     *
     * @param ProductSku $productSku
     * @return void
     */
    public function updating(ProductSku $productSku)
    {

    }

    /**
     * 监听数据更新后的事件。
     *
     * @param ProductSku $productSku
     * @return void
     */
    public function updated(ProductSku $productSku)
    {

    }

    /**
     * 监听数据即将保存的事件。
     *
     * @param ProductSku $productSku
     * @return void
     */
    public function saving(ProductSku $productSku)
    {

    }

    /**
     * 监听数据保存后的事件。
     *
     * @param ProductSku $productSku
     * @return void
     */
    public function saved(ProductSku $productSku)
    {

    }

    /**
     * 监听数据即将删除的事件。
     *
     * @param ProductSku $productSku
     * @return void
     */
    public function deleting(ProductSku $productSku)
    {

    }

    /**
     * 监听数据删除后的事件。
     *
     * @param ProductSku $productSku
     * @return void
     */
    public function deleted(ProductSku $productSku)
    {

    }

    /**
     * 监听数据即将从软删除状态恢复的事件。
     *
     * @param ProductSku $productSku
     * @return void
     */
    public function restoring(ProductSku $productSku)
    {

    }

    /**
     * 监听数据从软删除状态恢复后的事件。
     *
     * @param ProductSku $productSku
     * @return void
     */
    public function restored(ProductSku $productSku)
    {

    }

    public function defaultValues($productSku)
    {

    }
}
