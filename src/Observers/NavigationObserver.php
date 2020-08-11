<?php

namespace System\Observers;

use System\Models\Navigation;

class NavigationObserver
{

    /**
     * 监听数据即将创建的事件。
     *
     * @param Navigation $navigation
     * @return void
     */
    public function creating(Navigation $navigation)
    {

    }

    /**
     * 监听数据创建后的事件。
     *
     * @param Navigation $navigation
     * @return void
     */
    public function created(Navigation $navigation)
    {

    }

    /**
     * 监听数据即将更新的事件。
     *
     * @param Navigation $navigation
     * @return void
     */
    public function updating(Navigation $navigation)
    {

    }

    /**
     * 监听数据更新后的事件。
     *
     * @param Navigation $navigation
     * @return void
     */
    public function updated(Navigation $navigation)
    {

    }

    /**
     * 监听数据即将保存的事件。
     *
     * @param Navigation $navigation
     * @return void
     */
    public function saving(Navigation $navigation)
    {
        if (is_null($navigation->parent_id) || is_null($navigation->parent)) {
            $navigation->parent_id = 0;
        }
    }

    /**
     * 监听数据保存后的事件。
     *
     * @param Navigation $navigation
     * @return void
     */
    public function saved(Navigation $navigation)
    {

    }

    /**
     * 监听数据即将删除的事件。
     *
     * @param Navigation $navigation
     * @return void
     */
    public function deleting(Navigation $navigation)
    {

    }

    /**
     * 监听数据删除后的事件。
     *
     * @param Navigation $navigation
     * @return void
     */
    public function deleted(Navigation $navigation)
    {

    }

    /**
     * 监听数据即将从软删除状态恢复的事件。
     *
     * @param Navigation $navigation
     * @return void
     */
    public function restoring(Navigation $navigation)
    {

    }

    /**
     * 监听数据从软删除状态恢复后的事件。
     *
     * @param Navigation $navigation
     * @return void
     */
    public function restored(Navigation $navigation)
    {

    }
}
