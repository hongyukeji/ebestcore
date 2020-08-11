<?php

namespace System\Policies;

use System\Models\Order;
use System\Models\User;

class OrderPolicy
{
    public function view(?User $user, Order $order)
    {
        return $user->id === $order->user_id;
    }

    public function update(?User $user, Order $order)
    {
        return $user->id === $order->user_id;
    }

    public function delete(?User $user, Order $order)
    {
        return $user->id === $order->user_id;
    }

    public function before($user, $ability)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }
}
