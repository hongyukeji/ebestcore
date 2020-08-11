<?php

namespace System\Observers;

use System\Models\ProductImage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class ProductImageObserver
{
    /**
     * 监听数据即将创建的事件。
     *
     * @param ProductImage $productImage
     * @return void
     */
    public function creating(ProductImage $productImage)
    {

    }

    /**
     * 监听数据创建后的事件。
     *
     * @param ProductImage $productImage
     * @return void
     */
    public function created(ProductImage $productImage)
    {
        //
    }

    /**
     * 监听数据即将更新的事件。
     *
     * @param ProductImage $productImage
     * @return void
     */
    public function updating(ProductImage $productImage)
    {

    }

    /**
     * 监听数据更新后的事件。
     *
     * @param ProductImage $productImage
     * @return void
     */
    public function updated(ProductImage $productImage)
    {

    }

    /**
     * 监听数据即将保存的事件。
     *
     * @param ProductImage $productImage
     * @return void
     */
    public function saving(ProductImage $productImage)
    {
        //

    }

    /**
     * 监听数据保存后的事件。
     *
     * @param ProductImage $productImage
     * @return void
     */
    public function saved(ProductImage $productImage)
    {

    }

    /**
     * 监听数据即将删除的事件。
     *
     * @param ProductImage $productImage
     * @return void
     */
    public function deleting(ProductImage $productImage)
    {

    }

    /**
     * 监听数据删除后的事件。
     *
     * @param ProductImage $productImage
     * @return void
     */
    public function deleted(ProductImage $productImage)
    {
        if (Storage::exists($productImage->image_path)) {
            Storage::delete($productImage->image_path);
        }
    }

    /**
     * 监听数据即将从软删除状态恢复的事件。
     *
     * @param ProductImage $productImage
     * @return void
     */
    public function restoring(ProductImage $productImage)
    {

    }

    /**
     * 监听数据从软删除状态恢复后的事件。
     *
     * @param ProductImage $productImage
     * @return void
     */
    public function restored(ProductImage $productImage)
    {

    }
}
