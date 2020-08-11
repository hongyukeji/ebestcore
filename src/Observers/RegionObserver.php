<?php

namespace System\Observers;

use System\Models\Region;

class RegionObserver
{
    /**
     * 监听数据即将创建的事件。
     *
     * @param Region $region
     * @return void
     */
    public function creating(Region $region)
    {

    }

    /**
     * 监听数据创建后的事件。
     *
     * @param Region $region
     * @return void
     */
    public function created(Region $region)
    {

    }

    /**
     * 监听数据即将更新的事件。
     *
     * @param Region $region
     * @return void
     */
    public function updating(Region $region)
    {

    }

    /**
     * 监听数据更新后的事件。
     *
     * @param Region $region
     * @return void
     */
    public function updated(Region $region)
    {

    }

    /**
     * 监听数据即将保存的事件。
     *
     * @param Region $region
     * @return void
     */
    public function saving(Region $region)
    {
        if (is_null($region->parent_id) || is_null($region->parent)) {
            // 将父类设置为0, 自动修正
            $region->parent_id = 0;
            // 将层级设为 0
            $region->level = 0;
        } else {
            // 将层级设为父类目的层级 + 1
            $region->level = ($region->parent->level ?? 0) + 1;
        }
    }

    /**
     * 监听数据保存后的事件。
     *
     * @param Region $region
     * @return void
     */
    public function saved(Region $region)
    {

    }

    /**
     * 监听数据即将删除的事件。
     *
     * @param Region $region
     * @return void
     */
    public function deleting(Region $region)
    {

    }

    /**
     * 监听数据删除后的事件。
     *
     * @param Region $region
     * @return void
     */
    public function deleted(Region $region)
    {

    }

    /**
     * 监听数据即将从软删除状态恢复的事件。
     *
     * @param Region $region
     * @return void
     */
    public function restoring(Region $region)
    {

    }

    /**
     * 监听数据从软删除状态恢复后的事件。
     *
     * @param Region $region
     * @return void
     */
    public function restored(Region $region)
    {

    }
}
