<?php

namespace System\Models;

class UserFavorite extends Model
{
    /*
     * 收藏类型
     */
    public const FAVORITE_TYPE_PRODUCT = 1; // 商品
    public const FAVORITE_TYPE_ARTICLE = 2; // 文章
    public const FAVORITE_TYPE_SHOP = 3; // 店铺

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault(function ($user) {
            $user->id = 0;
            $user->name = '';
        });
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'favorite_id')->withDefault(function ($product) {
            $product->id = 0;
            $product->name = '';
        });
    }

    public function shop()
    {
        return $this->hasOne(Shop::class, 'id', 'favorite_id')->withDefault(function ($shop) {
            $shop->id = 0;
            $shop->name = config('shop.self_name', '平台自营');
        });
    }

    public function article()
    {
        return $this->hasOne(Article::class, 'id', 'favorite_id')->withDefault(function ($shop) {
            $shop->id = 0;
        });
    }
}
