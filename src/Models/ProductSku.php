<?php

namespace System\Models;

class ProductSku extends Model
{
    protected $fillable = [
        'product_id', 'sku_code', 'name', 'own_spec', 'image', 'price', 'market_price', 'cost_price', 'stock', 'sale_count',
        'is_default', 'sort', 'status',
    ];

    protected $guarded = [];

    /**
     * 追加到模型数组表单的访问器。
     *
     * @var array
     */
    protected $appends = ['attribute_array'];

    public function getAttributeArrayAttribute()
    {
        return explode(',', $this->name);
    }
}
