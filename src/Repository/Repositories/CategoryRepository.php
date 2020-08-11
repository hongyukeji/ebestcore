<?php

namespace System\Repository\Repositories;

use System\Models\Category;
use System\Repository\Interfaces\CategoryInterface;

class CategoryRepository extends Repository implements CategoryInterface
{
    public function findOne($id)
    {
        return Category::find($id);
    }

    public function findAll()
    {
        return Category::all();
    }

    public function findActiveAll()
    {
        return Category::query()->active()->orderBy('sort', 'desc')->get();
    }

    public function findTrees()
    {
        return Category::query()->with('children')->where('parent_id', false)->orderBy('sort', 'desc')->get();
    }

    public function findActiveTrees()
    {
        return Category::query()->with('children')->active()->where('parent_id', false)->orderBy('sort', 'desc')->get();
    }

    /*
     * 查找所有父类包括自己
     */
    public function findParents($id, $parents = [])
    {
        $parents = collect($parents);
        $category = Category::find($id);
        if ($category) {
            $parents->prepend($category);
            if (isset($category->parent_id)) {
                return $this->findParents($category->parent_id, $parents);
            }
        }
        return $parents;
    }
}
