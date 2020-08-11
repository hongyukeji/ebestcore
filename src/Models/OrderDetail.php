<?php

namespace System\Models;

class OrderDetail extends Model
{
    /**
     * 追加到模型数组表单的访问器。
     *
     * @var array
     */
    protected $appends = [
        'product_image_url',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id', 'id')->withDefault(function ($order) {
            $order->id = 0;
        });
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id')->withDefault(function ($product) {
            $product->id = 0;
            $product->name = '';
        });
    }

    public function productSku()
    {
        return $this->hasOne(ProductSku::class, 'id', 'product_sku_id')->withDefault(function ($product) {
            $product->id = 0;
            $product->name = '';
        });
    }

    public function getProductImageUrlAttribute()
    {
        if (!empty($this->product_image)) {
            return asset_url($this->product_image);
        } else {
            return asset_url(config('params.products.default_image'));
        }
    }
}
