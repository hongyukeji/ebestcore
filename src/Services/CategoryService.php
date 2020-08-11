<?php

namespace System\Services;

use System\Models\Brand;
use System\Models\Category;
use System\Models\CategoryToBrand;

class CategoryService extends Service
{
    /*
     * 获取当前分类的所有子分类
     */
    public function getChildren($id, $categories = null)
    {
        if (is_null($categories)) {
            $categories = collect([]);
        }
        if ($category = Category::with('children')->where('id', $id)->first()) {
            $categories->add($category);
            if ($category->children && count($category->children)) {
                foreach ($category->children as $item) {
                    $this->getChildren($item->id, $categories);
                }
            }
        }
        return $categories;
    }

    public function getBrands($category_id, $number)
    {
        $categories = $this->getChildren($category_id);
        $category_ids = $categories->pluck('id');
        $category_to_brands = CategoryToBrand::query()->whereIn('category_id', $category_ids)->get();
        $brands = $category_to_brands->map(function ($item, $key) {
            return $item->brand;
        });
        return $brands->sortBy('sort', SORT_DESC)->take($number);
    }

    public function getSpecification($category_id)
    {
        $category = Category::query()->find($category_id);

        // 从当前分类依次向上查找是否存在规格
        if ($category && $items = $category->getParents()->sortKeysDesc()) {
            foreach ($items as $item) {
                if ($item->specification->id != 0) {
                    return $item->specification;
                }
            }
        }
        return null;
    }
}
