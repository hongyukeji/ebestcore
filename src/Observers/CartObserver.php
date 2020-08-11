<?php

namespace System\Observers;

use System\Models\Cart;

class CartObserver
{

    /**
     * 监听数据即将创建的事件。
     *
     * @param Cart $cart
     * @return void
     */
    public function creating(Cart $cart)
    {

    }

    /**
     * 监听数据创建后的事件。
     *
     * @param Cart $cart
     * @return void
     */
    public function created(Cart $cart)
    {

    }

    /**
     * 监听数据即将更新的事件。
     *
     * @param Cart $cart
     * @return void
     */
    public function updating(Cart $cart)
    {

    }

    /**
     * 监听数据更新后的事件。
     *
     * @param Cart $cart
     * @return void
     */
    public function updated(Cart $cart)
    {

    }

    /**
     * 监听数据即将保存的事件。
     *
     * @param Cart $cart
     * @return void
     */
    public function saving(Cart $cart)
    {
        // 自动修正购物车商品数量
        if (is_null($cart->number)) {
            $cart->number = 0;
        }

        // 店铺编号
        if (is_null($cart->shop_id)) {
            $cart->shop_id = 0;
        }

        // 商品sku
        if (is_null($cart->product_sku_id)) {
            $cart->product_sku_id = 0;
        }
    }

    /**
     * 监听数据保存后的事件。
     *
     * @param Cart $cart
     * @return void
     */
    public function saved(Cart $cart)
    {

    }

    /**
     * 监听数据即将删除的事件。
     *
     * @param Cart $cart
     * @return void
     */
    public function deleting(Cart $cart)
    {

    }

    /**
     * 监听数据删除后的事件。
     *
     * @param Cart $cart
     * @return void
     */
    public function deleted(Cart $cart)
    {

    }

    /**
     * 监听数据即将从软删除状态恢复的事件。
     *
     * @param Cart $cart
     * @return void
     */
    public function restoring(Cart $cart)
    {

    }

    /**
     * 监听数据从软删除状态恢复后的事件。
     *
     * @param Cart $cart
     * @return void
     */
    public function restored(Cart $cart)
    {

    }
}
