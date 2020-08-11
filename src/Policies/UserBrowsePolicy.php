<?php

namespace System\Policies;

use System\Models\UserBrowse;
use System\Models\User;

class UserBrowsePolicy
{
    public function view(?User $user, UserBrowse $userBrowse)
    {
        return $user->id === $userBrowse->user_id;
    }

    public function update(?User $user, UserBrowse $userBrowse)
    {
        return $user->id === $userBrowse->user_id;
    }

    public function delete(?User $user, UserBrowse $userBrowse)
    {
        return $user->id === $userBrowse->user_id;
    }

    public function before($user, $ability)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }
}
