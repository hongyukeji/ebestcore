<?php

namespace System\Observers;

use System\Models\UserAddress;

class UserAddressObserver
{
    /**
     * 监听数据即将创建的事件。
     *
     * @param UserAddress $userAddress
     * @return void
     */
    public function creating(UserAddress $userAddress)
    {

    }

    /**
     * 监听数据创建后的事件。
     *
     * @param UserAddress $userAddress
     * @return void
     */
    public function created(UserAddress $userAddress)
    {

    }

    /**
     * 监听数据即将更新的事件。
     *
     * @param UserAddress $userAddress
     * @return void
     */
    public function updating(UserAddress $userAddress)
    {

    }

    /**
     * 监听数据更新后的事件。
     *
     * @param UserAddress $userAddress
     * @return void
     */
    public function updated(UserAddress $userAddress)
    {

    }

    /**
     * 监听数据即将保存的事件。
     *
     * @param UserAddress $userAddress
     * @return void
     */
    public function saving(UserAddress $userAddress)
    {
        // 检查默认地址
        //$userAddress->is_default = boolval($userAddress->is_default);
        if ($userAddress->is_default) {
            UserAddress::query()->where('user_id', $userAddress->user_id)->whereNotIn('id', [$userAddress->id])->update([
                'is_default' => false
            ]);
        }
    }

    /**
     * 监听数据保存后的事件。
     *
     * @param UserAddress $userAddress
     * @return void
     */
    public function saved(UserAddress $userAddress)
    {

    }

    /**
     * 监听数据即将删除的事件。
     *
     * @param UserAddress $userAddress
     * @return void
     * @throws \Exception
     */
    public function deleting(UserAddress $userAddress)
    {

    }

    /**
     * 监听数据删除后的事件。
     *
     * @param UserAddress $userAddress
     * @return void
     */
    public function deleted(UserAddress $userAddress)
    {

    }

    /**
     * 监听数据即将从软删除状态恢复的事件。
     *
     * @param UserAddress $userAddress
     * @return void
     */
    public function restoring(UserAddress $userAddress)
    {

    }

    /**
     * 监听数据从软删除状态恢复后的事件。
     *
     * @param UserAddress $userAddress
     * @return void
     */
    public function restored(UserAddress $userAddress)
    {

    }
}
