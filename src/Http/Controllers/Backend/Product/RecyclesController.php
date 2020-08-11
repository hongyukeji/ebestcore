<?php

namespace System\Http\Controllers\Backend\Product;

use System\Http\Controllers\Backend\Controller;
use System\Models\Product;
use Illuminate\Http\Request;

class RecyclesController extends Controller
{
    public function index(Request $request)
    {
        $pages = [
            'title' => trans('backend.commons.product_recycle'),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.product_recycles'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.product.recycles.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.recovery'),
                    'icon' => 'fa fa-recycle',
                    'class' => 'btn btn-info ajax-recovery',
                    'link' => 'javascript:;',
                ], [
                    'name' => trans('backend.commons.delete_completely'),
                    'icon' => 'fa fa-trash-o',
                    'class' => 'btn btn-danger ajax-delete',
                    'link' => 'javascript:;',
                ], [
                    'name' => trans('backend.commons.refresh'),
                    'icon' => 'fa fa-refresh',
                    'id' => '',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.product.recycles.index'),
                ],
            ],
        ];
        $filters = [];
        $builder = Product::onlyTrashed();

        // Search 参数用来模糊搜索数据
        if ($request->filled('search')) {
            $search = $request->input('search');
            $filters['search'] = $search;
            $like = "%{$search}%";
            $builder->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhere('spu_code', 'like', $like)
                    ->orWhere('keywords', 'like', $like);
            });
        }

        if ($request->filled('status')) {
            $builder->where('status', $request->input('status', true));
        }

        // 排序
        $sort_key = $request->input('order_by_column', 'deleted_at');
        $sort_value = $request->input('order_by_direction', 'desc');
        $filters['order_by_column'] = $sort_key;
        $filters['order_by_direction'] = $sort_value;
        $builder->orderBy($sort_key, $sort_value);

        // 分页
        $per_page = $request->input('per_page', config('params.pages.per_page'));
        $filters['per_page'] = $per_page;
        $items = $builder->paginate($per_page)->appends($filters);

        return view('backend::product.recycles.index', compact('pages', 'items'));
    }

    public function update($ids)
    {
        $products = Product::onlyTrashed()->find(explode(',', $ids)) or abort(404);

        foreach ($products as $product) {
            // 恢复软删除模型
            $product->restore();
        }

        if (request()->ajax()) {
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        return redirect()->route('backend.product.recycles.index')->with('message', trans('backend.messages.recovery_success'));
    }

    public function destroy($ids)
    {
        $products = Product::onlyTrashed()->find(explode(',', $ids)) or abort(404);

        foreach ($products as $product) {
            // 单个模型实例的永久删除...
            $product->forceDelete();
        }

        // 关联模型的永久删除...
        //$product->history()->forceDelete() or abort(404);

        if (request()->ajax()) {
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        return redirect()->route('backend.product.recycles.index')->with('message', trans('backend.messages.deleted_success'));
    }
}
