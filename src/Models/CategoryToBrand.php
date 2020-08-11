<?php

namespace System\Models;

class CategoryToBrand extends Model
{
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id')->withDefault(function ($category) {
            $category->id = 0;
            $category->name = trans('common.default') . trans('common.category');
            $category->full_name = trans('common.default') . trans('common.category');
        });
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id')->withDefault(function ($brand) {
            $brand->id = 0;
            $brand->name = '';
        });
    }
}
