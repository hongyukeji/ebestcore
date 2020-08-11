<?php

namespace System\Services;

use System\Models\ArticleCategory;

class ArticleCategoryService extends Service
{
    /*
     * 批量导入,包含children子类菜单
     */
    public function batchImport($items, $parent_id = 0)
    {
        foreach ($items as $item) {
            $orm_item = ArticleCategory::updateOrCreate([
                'name' => $item['name'] ?? null,
            ], [
                'parent_id' => $item['parent_id'] ?? $parent_id,
                'sort' => $item['sort'] ?? null,
                'status' => $item['status'] ?? null,
                'created_at' => $item['created_at'] ?? now(),
                'updated_at' => $item['updated_at'] ?? now(),
            ]);
            if (isset($item['children']) && count($item['children'])) {
                $this->batchImport($item['children'], $orm_item->id);
            }
        }
    }
}
