<?php

namespace System\Models;

class CategoryToSpecification extends Model
{
    public function category()
    {
        return $this->hasOne(Category::class);
    }

    public function specification()
    {
        return $this->hasOne(ProductSpecification::class);
    }
}
