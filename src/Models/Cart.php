<?php

namespace System\Models;

class Cart extends Model
{
    /**
     * 追加到模型数组表单的访问器。
     *
     * @var array
     */
    protected $appends = [
        'price', 'product_price', 'product_stock', 'product_subtotal',
    ];

    public function shop()
    {
        return $this->hasOne(Shop::class, 'id', 'shop_id')->withDefault(function ($shop) {
            $shop->id = 0;
            $shop->name = config('shop.self_name', '平台自营');
            $shop->support_invoice = config('shop.support_invoice', false);
        });
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id')->withDefault(function ($product) {
            $product->id = 0;
        });
    }

    public function productSku()
    {
        return $this->hasOne(ProductSku::class, 'id', 'product_sku_id')->withDefault(function ($productSku) {
            $productSku->id = 0;
        });
    }

    public function getPriceAttribute()
    {
        return $this->productSku->price ? $this->productSku->price : $this->product->price;
    }

    public function getStockAttribute()
    {
        return $this->productSku->stock ? $this->productSku->stock : $this->product->stock;
    }

    public function getProductPriceAttribute()
    {
        return $this->productSku->price ? $this->productSku->price : $this->product->price;
    }

    public function getProductStockAttribute()
    {
        return $this->productSku->stock ? $this->productSku->stock : $this->product->stock;
    }

    public function getProductSubtotalAttribute()
    {
        return number_format($this->product_price * $this->number, 2, '.', '');
    }
}
