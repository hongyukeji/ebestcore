<?php

namespace System\Observers;

use System\Models\User;
use System\Models\UserAccount;
use System\Models\UserExtend;

class UserObserver
{
    /**
     * 监听数据即将创建的事件。
     *
     * @param User $user
     * @return void
     */
    public function creating(User $user)
    {
        // 状态
        if (is_null($user->status)) {
            $user->status = true;
        }
        // 创建时间
        if (is_null($user->created_at)) {
            $user->created_at = now();
        }
        // 更新时间
        if (is_null($user->updated_at)) {
            $user->updated_at = now();
        }
    }

    /**
     * 监听数据创建后的事件。
     *
     * @param User $user
     * @return void
     */
    public function created(User $user)
    {
        // 创建用户账户表
        UserAccount::firstOrCreate([
            'user_id' => $user->id
        ]);
        // 创建用户扩展表
        UserExtend::firstOrCreate([
            'user_id' => $user->id
        ]);
    }

    /**
     * 监听数据即将更新的事件。
     *
     * @param User $user
     * @return void
     */
    public function updating(User $user)
    {

    }

    /**
     * 监听数据更新后的事件。
     *
     * @param User $user
     * @return void
     */
    public function updated(User $user)
    {

    }

    /**
     * 监听数据即将保存的事件。
     *
     * @param User $user
     * @return void
     */
    public function saving(User $user)
    {

    }

    /**
     * 监听数据保存后的事件。
     *
     * @param User $user
     * @return void
     */
    public function saved(User $user)
    {

    }

    /**
     * 监听数据即将删除的事件。
     *
     * @param User $user
     * @return void
     * @throws \Exception
     */
    public function deleting(User $user)
    {
        // 删除用户对应的账户表数据
        $user->account->delete();
        //UserAccount::query()->find($user->id)->delete();
        // 删除用户对应的扩展表数据
        $user->extend->delete();
        //UserExtend::query()->find($user->id)->delete();
    }

    /**
     * 监听数据删除后的事件。
     *
     * @param User $user
     * @return void
     */
    public function deleted(User $user)
    {

    }

    /**
     * 监听数据即将从软删除状态恢复的事件。
     *
     * @param User $user
     * @return void
     */
    public function restoring(User $user)
    {

    }

    /**
     * 监听数据从软删除状态恢复后的事件。
     *
     * @param User $user
     * @return void
     */
    public function restored(User $user)
    {

    }
}
