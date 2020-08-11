<?php

namespace System\Policies;

use System\Models\Product;
use System\Models\User;

class ProductPolicy
{
    public function update(?User $user, Product $product)
    {
        return $user->id === $product->shop_id->user_id;
    }

    public function delete(?User $user, Product $product)
    {
        return $user->id === $product->shop_id->user_id;
    }

    public function before($user, $ability)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }
}
