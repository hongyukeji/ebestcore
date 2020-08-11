<?php

namespace System\Models;

use Illuminate\Database\Eloquent\Model;

class ProductComment extends Model
{
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault(function ($user) {
            $user->id = 0;
            $user->name = '默认用户';
        });
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id')->withDefault(function ($order) {
            $order->id = 0;
        });
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id')->withDefault(function ($product) {
            $product->id = 0;
        });
    }

    public function productSku()
    {
        return $this->belongsTo(ProductSku::class, 'product_sku_id', 'id')->withDefault(function ($productSku) {
            $productSku->id = 0;
        });
    }
}
