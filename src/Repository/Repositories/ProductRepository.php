<?php

namespace System\Repository\Repositories;

use Illuminate\Support\Carbon;
use System\Models\Product;
use System\Repository\Interfaces\ProductInterface;
use System\Services\CategoryService;
use System\Traits\Repository\CountRepositoryTrait;

class ProductRepository extends Repository implements ProductInterface
{
    use CountRepositoryTrait;

    public function createCountModel()
    {
        return Product::query();
    }

    public function createCount($shop_id = null)
    {
        $data = [];

        // 时间 whereTime / 日期 whereDate

        // 今天数据
        $data['today_count'] = self::createCountModel()->whereDate('created_at', Carbon::today())->count();
        // 昨天数据
        $data['yesterday_count'] = self::createCountModel()->whereDate('created_at', Carbon::yesterday())->count();
        // 本周数据
        $this_week = [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()];
        $data['this_week_count'] = self::createCountModel()->whereBetween('created_at', $this_week)->count();
        // 上周数据
        $last_week = [Carbon::now()->startOfWeek()->subWeek(), Carbon::now()->endOfWeek()->subWeek()];
        $data['last_week_count'] = self::createCountModel()->whereBetween('created_at', $last_week)->count();
        // 本月数据
        $data['this_month_count'] = self::createCountModel()->whereMonth('created_at', Carbon::now()->month)->count();
        // 上月数据
        $data['last_month_count'] = self::createCountModel()->whereMonth('created_at', Carbon::now()->subMonth()->month)->count();
        // 本年数据
        $data['this_year_count'] = self::createCountModel()->whereYear('created_at', Carbon::now()->year)->count();
        // 上一年
        $data['last_year_count'] = self::createCountModel()->whereYear('created_at', Carbon::now()->subYear()->year)->count();

        return $data;
    }

    public function findOne($id)
    {
        return Product::find($id);
    }

    public function findAll()
    {
        return Product::all();
    }

    public function search($shop_id = null)
    {
        $request = request();

        $builder = Product::query()->with(['extend', 'category', 'brand', 'shop', 'images', 'comments', 'skus']);
        if ($shop_id) {
            $builder->where('shop_id', $shop_id);
        }
        // 商品SPU
        if ($request->filled('spu_code')) {
            $builder->where('spu_code', $request->input('spu_code'));
        }

        // 商品名称
        if ($request->filled('name')) {
            $name = $request->input('name');
            $builder->where('name', 'like', "%{$name}%");
        }

        // 商品关键词
        if ($request->filled('keywords')) {
            $keywords = $request->input('keywords');
            $like = "%{$keywords}%";
            $builder->where('keywords', 'like', $like);
        }

        // Search 参数用来模糊搜索数据
        if ($request->filled('search')) {
            $search = $request->input('search');
            foreach (explode(' ', $search) as $key) {
                $like = "%{$key}%";
                $builder->where(function ($query) use ($like) {
                    $query->where('name', 'like', $like)
                        ->orWhere('description', 'like', $like)
                        ->orWhere('spu_code', 'like', $like)
                        ->orWhere('keywords', 'like', $like)
                        ->orWhereHas('shop', function ($query) use ($like) {
                            $query->where('name', 'like', $like);
                        });
                });
            }
        }

        // 商品分类
        if ($request->filled('category_id')) {
            $category_id = $request->input('category_id');
            $ids = (new CategoryService())->getChildren($category_id)->pluck('id');
            $builder->whereIn('category_id', $ids);
        }

        // 商品品牌
        if ($request->filled('brand_id')) {
            $builder->where('brand_id', $request->input('brand_id'));
        }

        // 商品店铺
        if ($request->filled('shop_id')) {
            $builder->where('shop_id', $request->input('shop_id'));
        }

        // 商品最小价格
        if ($request->filled('min_price')) {
            $min_price = $request->input('min_price', 0);
            $builder->where('price', '>=', $min_price);
        }

        // 商品最大价格
        if ($request->filled('max_price')) {
            $max_price = $request->input('max_price', 0);
            $builder->where('price', '<=', $max_price);
        }

        // 商品最小销量
        if ($request->filled('min_sale_count')) {
            $min_sale_count = $request->input('min_sale_count', 0);
            $builder->where('sale_count', '>=', $min_sale_count);
        }

        // 商品最大销量
        if ($request->filled('max_sale_count')) {
            $max_sale_count = $request->input('max_sale_count', 0);
            $builder->where('sale_count', '<=', $max_sale_count);
        }

        // 是否精品
        if ($request->filled('is_best')) {
            $builder->where('is_best', $request->input('is_best'));
        }

        // 是否热卖
        if ($request->filled('is_hot')) {
            $builder->where('is_hot', $request->input('is_hot'));
        }

        // 是否新品
        if ($request->filled('is_new')) {
            $builder->where('is_new', $request->input('is_new'));
        }

        // 排序
        if ($request->filled('sort')) {
            $builder->orderBy($request->input('sort', 'sort'), $request->input('order', 'desc'));
        } else {
            $builder->orderBy($request->input('sort', 'id'), $request->input('order', 'desc'));
        }

        // 审核状态
        if ($request->filled('audit_status')) {
            $builder->where('audit_status', $request->input('audit_status'));
        }

        // 商品状态
        if ($request->filled('status')) {
            $builder->where('status', $request->input('status'));
        }

        // 分页
        $per_page = $request->input('per_page', config('params.pages.per_page', 15));

        /*
        // 添加过滤条件 ->appends($filters)
        $filters = ['status' => true];
        $builder->macro('filters', function () use ($filters) {
            return $filters;
        });
        */

        return $builder->paginate($per_page)->appends(request()->query());
    }
}
