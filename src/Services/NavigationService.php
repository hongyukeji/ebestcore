<?php

namespace System\Services;

use System\Models\Navigation;

class NavigationService extends Service
{
    /*
     * 批量导入,包含children子类菜单
     */
    public function batchImport($items, $parent_id = 0)
    {
        foreach ($items as $item) {
            $orm_item = Navigation::updateOrCreate([
                'name' => $item['name'] ?? null,
                'title' => $item['title'] ?? null,
                'group' => $item['group'] ?? null,
                'image' => $item['image'] ?? null,
            ], [
                'link' => $item['link'] ?? null,
                'description' => $item['description'] ?? null,
                'content' => $item['content'] ?? null,
                'icon' => $item['icon'] ?? null,
                'target' => $item['target'] ?? null,
                'group_name' => $item['group_name'] ?? null,
                'parent_id' => $parent_id ?? 0,
                'sort' => $item['sort'] ?? null,
                'status' => $item['status'] ?? true,
                'created_at' => $item['created_at'] ?? now(),
                'updated_at' => $item['updated_at'] ?? now(),
            ]);
            if (isset($item['children']) && count($item['children'])) {
                $this->batchImport($item['children'], $orm_item->id);
            }
        }
    }
}
