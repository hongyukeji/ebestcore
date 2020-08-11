<?php

namespace System\Observers;

use System\Models\Category;
use System\Models\CategoryToBrand;

class CategoryObserver
{
    public function saving(Category $category)
    {
        // 修正父类id
        if ($category->id == $category->parent_id) {
            $category->parent_id = 0;
        }

        // 默认状态
        if (is_null($category->status)) {
            $category->status = true;
        }

        // 默认排序
        if (is_null($category->sort)) {
            $sort = config('params.models.sort_default', 1000);
            $category->sort = $sort;
        }

        // 如果创建的是一个根类目
        if (is_null($category->parent_id) || is_null($category->parent)) {
            // 将父类设置为0, 自动修正
            $category->parent_id = 0;
        }
    }

    /**
     * 监听数据保存后的事件。
     *
     * @param Category $category
     * @return void
     */
    public function saved(Category $category)
    {
        $request = request();

        if ($request->filled("brand_ids")) {
            $brand_ids = $request->input('brand_ids', []);
            foreach ($request->input('brand_ids') as $brand_id) {
                CategoryToBrand::updateOrCreate(['category_id' => $category->id, 'brand_id' => $brand_id]);
            }
            CategoryToBrand::query()->where('category_id', $category->id)->whereNotIn('brand_id', $brand_ids)->delete();
        } else {
            CategoryToBrand::query()->where('category_id', $category->id)->delete();
        }
    }
}
