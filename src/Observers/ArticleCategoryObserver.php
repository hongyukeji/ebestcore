<?php

namespace System\Observers;

use System\Models\ArticleCategory;

class ArticleCategoryObserver
{
    public function saving(ArticleCategory $articleCategory)
    {
        // 修正父类id
        if ($articleCategory->id == $articleCategory->parent_id) {
            $articleCategory->parent_id = 0;
        }

        // 默认状态
        if (is_null($articleCategory->status)) {
            $articleCategory->status = true;
        }

        // 默认排序
        if (is_null($articleCategory->sort)) {
            $sort = config('params.models.sort_default', 1000);
            $articleCategory->sort = $sort;
        }

        // 如果创建的是一个根类目
        if (is_null($articleCategory->parent_id) || is_null($articleCategory->parent)) {
            // 将父类设置为0, 自动修正
            $articleCategory->parent_id = 0;
        }
    }

    /**
     * 监听数据保存后的事件。
     *
     * @param ArticleCategory $articleCategory
     * @return void
     */
    public function saved(ArticleCategory $articleCategory)
    {
        //
    }
}
