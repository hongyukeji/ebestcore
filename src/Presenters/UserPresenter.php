<?php

namespace System\Presenters;

use System\Models\Cart;
use System\Models\UserBrowse;
use System\Models\UserFavorite;
use Illuminate\Support\Facades\Auth;

class UserPresenter
{
    public function getFavoriteProducts()
    {
        if (Auth::check()) {
            $user_id = Auth::id();
            return UserFavorite::query()->with(['product'])->where([
                'user_id' => $user_id,
                'favorite_type' => UserFavorite::FAVORITE_TYPE_PRODUCT,
            ])->paginate(15);
        } else {
            return null;
        }
    }

    public function getFavoriteShops()
    {
        if (Auth::check()) {
            $user_id = Auth::id();
            return UserFavorite::query()->with(['shop'])->where([
                'user_id' => $user_id,
                'favorite_type' => UserFavorite::FAVORITE_TYPE_SHOP,
            ])->paginate(15);
        } else {
            return null;
        }
    }

    public function getBrowseProducts()
    {
        if (Auth::check()) {
            $user_id = Auth::id();
            return UserBrowse::query()->with(['product'])->where([
                'user_id' => $user_id,
                'browse_type' => UserBrowse::BROWSE_TYPE_PRODUCT,
            ])->paginate(15);
        } else {
            return null;
        }
    }
}
