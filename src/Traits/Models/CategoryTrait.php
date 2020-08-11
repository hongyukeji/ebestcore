<?php

namespace System\Traits\Models;

use Illuminate\Support\Str;

trait CategoryTrait
{
    public $name_symbol = '/';

    /**
     * 获取父类
     *
     * @return mixed
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id', 'id')->active()->sorted()->withDefault([
            'id' => 0,
            'name' => trans('backend.commons.default_category'),
            'full_name' => trans('backend.commons.top_level_category'),
            'parent_name' => trans('backend.commons.top_level_category'),
        ]);
    }

    /**
     * 获取父分类
     *
     * @return mixed
     */
    public function parents()
    {
        return $this->parent()->with('parents')->active()->sorted();
        //return $this->belongsTo(self::class);
        // search_multi_array($parents, 'id')
    }

    /*
     * 查找所有父类包括自己
     */
    public function getParents($id = null, $parents = [])
    {
        if (is_null($id)) {
            $id = $this->id;
        }
        $parents = collect($parents);
        $item = self::find($id);
        if ($item) {
            $parents->prepend($item);
            if (isset($item->parent_id)) {
                return $this->getParents($item->parent_id, $parents);
            }
        }
        return $parents;
    }

    /*
     * 获取所有分类名称(包含当前分类)
     */
    public function getFullNameAttribute()
    {
        $parents = $this->getParents();
        if ($parents->count() < 1) {
            $parents->prepend(collect([
                'id' => 0,
                'name' => trans('backend.commons.default_category'),
                'full_name' => trans('backend.commons.top_level_category'),
                'parent_name' => trans('backend.commons.top_level_category'),
            ]));
        }
        return $parents->pluck('name')->implode($this->name_symbol);
    }

    /*
     * 获取所有父类名称(不包含当前分类)
     */
    public function getParentNameAttribute()
    {
        $parents = $this->getParents()->filter(function ($value, $key) {
            return $value->id != $this->id;
        });
        if ($parents->count() < 1) {
            $parents->prepend(collect([
                'id' => 0,
                'name' => trans('backend.commons.top_level_category'),
                'full_name' => trans('backend.commons.top_level_category'),
                'parent_name' => trans('backend.commons.top_level_category'),
            ]));
        }
        return $parents->pluck('name')->implode($this->name_symbol);
    }

    /**
     * 获取子类
     *
     * @return mixed
     */
    public function child()
    {
        return $this->hasMany(self::class, 'parent_id', 'id')->active()->sorted();
    }

    /**
     * 获取所有子分类
     *
     * @return mixed
     */
    public function children()
    {
        // $categories = \System\Models\Category::with('children')->where('parent_id',0)->get();
        // $categories->children;
        // $categories->children->first()->children;
        return $this->child()->with('children');
    }

    public function getTrees()
    {
        return self::with('children')->where('parent_id', false)->active()->sorted()->get();
    }
}
