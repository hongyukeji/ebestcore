<?php

namespace System\Policies;

use System\Models\Cart;
use System\Models\User;

class CartPolicy
{
    public function view(?User $user, Cart $cart)
    {
        return $user->id === $cart->user_id;
    }

    public function update(?User $user, Cart $cart)
    {
        return $user->id === $cart->user_id;
    }

    public function delete(?User $user, Cart $cart)
    {
        return $user->id === $cart->user_id;
    }

    public function before($user, $ability)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }
}
