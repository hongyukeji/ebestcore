<?php

namespace System\Observers;

use System\Models\UserAccount;

class UserAccountObserver
{
    /**
     * 监听数据即将创建的事件。
     *
     * @param UserAccount $userAccount
     * @return void
     */
    public function creating(UserAccount $userAccount)
    {

    }

    /**
     * 监听数据创建后的事件。
     *
     * @param UserAccount $userAccount
     * @return void
     */
    public function created(UserAccount $userAccount)
    {

    }

    /**
     * 监听数据即将更新的事件。
     *
     * @param UserAccount $userAccount
     * @return void
     */
    public function updating(UserAccount $userAccount)
    {

    }

    /**
     * 监听数据更新后的事件。
     *
     * @param UserAccount $userAccount
     * @return void
     */
    public function updated(UserAccount $userAccount)
    {

    }

    /**
     * 监听数据即将保存的事件。
     *
     * @param UserAccount $userAccount
     * @return void
     */
    public function saving(UserAccount $userAccount)
    {

    }

    /**
     * 监听数据保存后的事件。
     *
     * @param UserAccount $userAccount
     * @return void
     */
    public function saved(UserAccount $userAccount)
    {
        // 如果用户金额减少
        if ($userAccount->getOriginal('money') > $userAccount->money) {
            // 增加已消费金额
            $userAccount->increment('spend_money', $userAccount->getOriginal('money') - $userAccount->money);
            // TODO: 添加余额记录
        }
    }

    /**
     * 监听数据即将删除的事件。
     *
     * @param UserAccount $userAccount
     * @return void
     * @throws \Exception
     */
    public function deleting(UserAccount $userAccount)
    {

    }

    /**
     * 监听数据删除后的事件。
     *
     * @param UserAccount $userAccount
     * @return void
     */
    public function deleted(UserAccount $userAccount)
    {

    }

    /**
     * 监听数据即将从软删除状态恢复的事件。
     *
     * @param UserAccount $userAccount
     * @return void
     */
    public function restoring(UserAccount $userAccount)
    {

    }

    /**
     * 监听数据从软删除状态恢复后的事件。
     *
     * @param UserAccount $userAccount
     * @return void
     */
    public function restored(UserAccount $userAccount)
    {

    }
}
