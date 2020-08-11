<?php

namespace System\Policies;

use System\Models\UserFavorite;
use System\Models\User;

class UserFavoritePolicy
{
    public function view(?User $user, UserFavorite $userFavorite)
    {
        return $user->id === $userFavorite->user_id;
    }

    public function update(?User $user, UserFavorite $userFavorite)
    {
        return $user->id === $userFavorite->user_id;
    }

    public function delete(?User $user, UserFavorite $userFavorite)
    {
        return $user->id === $userFavorite->user_id;
    }

    public function before($user, $ability)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }
}
