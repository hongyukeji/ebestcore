<?php

namespace System\Observers;

use System\Models\Admin;

class AdminObserver
{

    /**
     * 监听数据即将创建的事件。
     *
     * @param Admin $admin
     * @return void
     */
    public function creating(Admin $admin)
    {
        // 状态
        if (is_null($admin->status)) {
            $admin->status = true;
        }
        // 创建时间
        if (is_null($admin->created_at)) {
            $admin->created_at = now();
        }
        // 更新时间
        if (is_null($admin->updated_at)) {
            $admin->updated_at = now();
        }
    }

    /**
     * 监听数据创建后的事件。
     *
     * @param Admin $admin
     * @return void
     */
    public function created(Admin $admin)
    {

    }

    /**
     * 监听数据即将更新的事件。
     *
     * @param Admin $admin
     * @return void
     */
    public function updating(Admin $admin)
    {

    }

    /**
     * 监听数据更新后的事件。
     *
     * @param Admin $admin
     * @return void
     */
    public function updated(Admin $admin)
    {

    }

    /**
     * 监听数据即将保存的事件。
     *
     * @param Admin $admin
     * @return void
     */
    public function saving(Admin $admin)
    {

    }

    /**
     * 监听数据保存后的事件。
     *
     * @param Admin $admin
     * @return void
     */
    public function saved(Admin $admin)
    {

    }

    /**
     * 监听数据即将删除的事件。
     *
     * @param Admin $admin
     * @return void
     */
    public function deleting(Admin $admin)
    {

    }

    /**
     * 监听数据删除后的事件。
     *
     * @param Admin $admin
     * @return void
     */
    public function deleted(Admin $admin)
    {

    }

    /**
     * 监听数据即将从软删除状态恢复的事件。
     *
     * @param Admin $admin
     * @return void
     */
    public function restoring(Admin $admin)
    {

    }

    /**
     * 监听数据从软删除状态恢复后的事件。
     *
     * @param Admin $admin
     * @return void
     */
    public function restored(Admin $admin)
    {

    }
}
