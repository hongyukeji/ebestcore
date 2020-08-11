<?php

namespace System\Models;

class ProductImage extends Model
{
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id')->withDefault(function ($product) {
            $product->id = 0;
        });
    }
}
