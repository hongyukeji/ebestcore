<?php

namespace System\Http\Controllers\Backend\Product;

use System\Models\Brand;
use System\Http\Controllers\Backend\Controller;
use System\Http\Requests\BrandRequest;
use Illuminate\Http\Request;

class BrandsController extends Controller
{
    public function index(Request $request)
    {
        $pages = [
            'title' => trans('backend.commons.brands'),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.brands'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.product.brands.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.product.brands.create'),
                ], [
                    'name' => trans('backend.commons.delete'),
                    'icon' => 'fa fa-trash-o',
                    'class' => 'btn btn-danger ajax-delete',
                    'link' => 'javascript:;',
                ], [
                    'name' => trans('backend.commons.refresh'),
                    'icon' => 'fa fa-refresh',
                    'id' => '',
                    'class' => 'btn btn-outline btn-default',
                    'link' => route('backend.product.brands.index'),
                ],
            ],
        ];

        $filters = [];
        $builder = Brand::query();

        // Search 参数用来模糊搜索数据
        if ($request->filled('search')) {
            $search = $request->input('search');
            $filters['search'] = $search;
            $like = "%{$search}%";
            $builder->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhere('description', 'like', $like);
            });
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            $builder->where('status', $status);
        }

        // 排序
        $sort_key = $request->input('order_by_column', 'id');
        $sort_value = $request->input('order_by_direction', 'desc');
        $filters['order_by_column'] = $sort_key;
        $filters['order_by_direction'] = $sort_value;
        $builder->orderBy($sort_key, $sort_value);

        // 分页
        $per_page = $request->input('per_page', config('params.pages.per_page'));
        $items = $builder->paginate($per_page)->appends($filters);
        $filters['per_page'] = $per_page;

        return view('backend::product.brands.index', compact('items', 'pages'));
    }

    public function show(Brand $brand)
    {
        $brand or abort(404);
        $pages = [
            'title' => trans('backend.commons.show_action', ['option' => trans('backend.commons.brand')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.brands'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.product.brands.index'),
                ], [
                    'name' => trans('backend.commons.show_action', ['option' => trans('backend.commons.brand')]),
                    'icon' => 'fa fa-eye',
                    'link' => route('backend.product.brands.show', $brand->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.product.brands.create'),
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.brand')]),
                    'icon' => 'fa fa-edit',
                    'class' => 'btn btn-warning',
                    'link' => route('backend.product.brands.edit', $brand->id),
                ],
            ],
        ];
        return view('backend::product.brands.show', compact('brand', 'pages'));
    }

    public function create(Brand $brand)
    {
        $pages = [
            'title' => trans('backend.commons.add_action', ['option' => trans('backend.commons.brand')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.brands'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.product.brands.index'),
                ], [
                    'name' => trans('backend.commons.add_action', ['option' => trans('backend.commons.brand')]),
                    'icon' => 'fa fa-plus',
                    'link' => route('backend.product.brands.create'),
                    'active' => true,
                ],
            ],
        ];
        return view('backend::product.brands.create_and_edit', compact('pages', 'brand'));
    }

    public function store(BrandRequest $request)
    {
        $brand = Brand::create($request->all());
        return redirect()->route('backend.product.brands.show', $brand->id)->with('message', trans('backend.messages.created_success'));
    }

    public function edit(Brand $brand)
    {
        $brand or abort(404);
        $pages = [
            'title' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.brand')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.brands'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.product.brands.index'),
                    'active' => true,
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.brand')]),
                    'icon' => 'fa fa-edit',
                    'link' => route('backend.product.brands.edit', $brand->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.look'),
                    'icon' => 'fa fa-eye',
                    'class' => 'btn btn-info',
                    'link' => route('backend.product.brands.show', $brand->id),
                ],
            ],
        ];
        return view('backend::product.brands.create_and_edit', compact('brand', 'pages'));
    }

    public function update(BrandRequest $request, Brand $brand)
    {
        $brand or abort(404);
        $brand->update($request->all());
        return redirect()->route('backend.product.brands.show', $brand->id)->with('message', trans('backend.messages.updated_success'));
    }

    public function destroy($id)
    {
        Brand::destroy(explode(',', $id)) or abort(404);
        if (request()->ajax()) {
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        return redirect()->route('backend.product.brands.index')->with('message', trans('backend.messages.deleted_success'));
    }
}
