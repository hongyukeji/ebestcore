<?php

namespace System\Policies;

use System\Models\Shop;
use System\Models\User;

class ShopPolicy
{
    public function update(?User $user, Shop $shop)
    {
        return $user->id === $shop->user_id;
    }

    public function delete(?User $user, Shop $shop)
    {
        return $user->id === $shop->user_id;
    }

    public function before($user, $ability)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }
}
