<?php

namespace System\Repository\Repositories;

use System\Models\Brand;
use System\Models\Category;
use System\Repository\Interfaces\BrandInterface;

class BrandRepository extends Repository implements BrandInterface
{
    public function findAll()
    {
        return Brand::all();
    }

    public function findOne($id)
    {
        return Brand::query()->find($id);
    }

    public function search()
    {
        $request = request();
        $filters = [];
        $builder = Brand::query();

        // 名称
        if ($request->filled('name')) {
            $name = $request->input('name');
            $filters['name'] = $name;
            $builder->where('name', 'like', "%{$name}%");
        }

        // 状态
        if ($request->filled('status')) {
            $builder->where('status', $request->input('status'));
            $filters['status'] = $request->input('status');
        }

        // 排序
        if ($request->filled('order_by_column') || !$request->has('order_by_column')) {
            $builder->orderBy(
                $request->input('order_by_column', 'sort'),
                $request->input('order_by_param', 'desc')
            );
            $filters['order_by_column'] = $request->input('order_by_column');
            $filters['order_by_param'] = $request->input('order_by_param');
        }

        if ($request->filled('per_page')) {
            $items = $builder->paginate($request->input('per_page'));
            $items->appends($filters);
            $filters['per_page'] = $request->input('per_page');
        } else {
            $items = $builder->get();
        }

        return $items;
    }
}
