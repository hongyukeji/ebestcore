<?php

namespace System\Services;

use System\Models\Menu;

class MenuService extends Service
{
    /*
     * 批量导入菜单,包含children子类菜单
     */
    public function batchImport($items, $parent_id = 0)
    {
        foreach ($items as $item) {
            $orm_item = Menu::updateOrCreate([
                'name' => $item['name'] ?? '',
                'uri' => $item['uri'] ?? '',
                'label' => $item['label'] ?? '',
            ], [
                'permission' => $item['permission'] ?? null,
                'icon' => $item['icon'] ?? null,
                'target' => $item['target'] ?? null,
                'parent_id' => $parent_id ?? 0,
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
