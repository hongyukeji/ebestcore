<?php

namespace System\Repository\Repositories;

use System\Models\Region;
use System\Repository\Interfaces\RegionInterface;

class RegionRepository extends Repository implements RegionInterface
{
    public function findOne($id)
    {
        return Region::find($id);
    }

    public function findAll()
    {
        return Region::all();
    }

    public function findActiveAll()
    {
        return Region::query()->active()->orderBy('sort', 'desc')->get();
    }

    public function findTrees()
    {
        return Region::query()->with('children')->where('parent_id', false)->orderBy('sort', 'desc')->get();
    }

    public function findActiveTrees()
    {
        return Region::query()->with('children')->active()->where('parent_id', false)->orderBy('sort', 'desc')->get();
    }

    /*
     * 查找所有父类包括自己
     */
    public function findParents($id, $parents = [])
    {
        $parents = collect($parents);
        $category = Region::find($id);
        if ($category) {
            $parents->prepend($category);
            if (isset($category->parent_id)) {
                return $this->findParents($category->parent_id, $parents);
            }
        }
        return $parents;
    }
}
