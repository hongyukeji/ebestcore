<?php

namespace System\Repository\Repositories;

use System\Models\Menu;
use System\Repository\Interfaces\MenuInterface;

class MenuRepository extends Repository implements MenuInterface
{
    public function findAll()
    {
        return Menu::all();
    }

    public function findOne($id)
    {
        return Menu::query()->find($id);
    }

    public function findActiveAll()
    {
        return Menu::query()->active()->orderBy('sort', 'desc')->get();
    }

    public function findTrees()
    {
        return Menu::query()->with('children')->where('parent_id', false)->orderBy('sort', 'desc')->get();
    }

    public function findActiveTrees()
    {
        return Menu::query()->with('children')->active()->where('parent_id', false)->orderBy('sort', 'desc')->get();
    }

    /*
     * 查找所有父类包括自己
     */
    public function findParents($id, $parents = [])
    {
        $parents = collect($parents);
        $menu = Menu::find($id);
        if ($menu) {
            $parents->prepend($menu);
            if (isset($menu->parent_id)) {
                return $this->findParents($menu->parent_id, $parents);
            }
        }
        return $parents;
    }
}
