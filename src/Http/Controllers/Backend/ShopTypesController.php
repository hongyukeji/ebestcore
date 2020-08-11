<?php

namespace System\Http\Controllers\Backend;

use System\Models\ShopType;
use System\Http\Requests\ShopTypeRequest;
use Illuminate\Http\Request;

class ShopTypesController extends Controller
{
    public function index(Request $request)
    {
        $pages = [
            'title' => trans('backend.commons.shop_types'),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.shop_types'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.shop-types.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.shop-types.create'),
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
                    'link' => route('backend.shop-types.index'),
                ],
            ],
        ];

        $filters = [];
        $builder = ShopType::query();

        // Search 参数用来模糊搜索数据
        if ($request->filled('search')) {
            $filters['search'] = $request->input('search');
            $search = "%{$filters['search']}%";
            $builder->where(function ($query) use ($search) {
                $query->where('name', 'like', $search)
                    ->orWhere('description', 'like', $search);
            });
        }

        if ($request->filled('status')) {
            $filters['status'] = $request->input('status');
            $builder->where('status', $filters['status']);
        }

        // 排序
        $filters['shop_by_column'] = $request->input('shop_by_column', 'id');
        $filters['shop_by_direction'] = $request->input('shop_by_direction', 'desc');
        $builder->orderBy($filters['shop_by_column'], $filters['shop_by_direction']);

        if (request()->ajax()) {
            $data = $builder->get();
            return response()->json(api_result(0, null, $data), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }

        // 分页
        $filters['per_page'] = $request->input('per_page', config('params.pages.per_page', 15));
        $items = $builder->paginate($filters['per_page'])->appends($filters);

        return view('backend::shop-types.index', compact('items', 'pages'));
    }

    public function show(ShopType $shopType)
    {
        $shopType or abort(404);
        $pages = [
            'title' => trans('backend.commons.show_action', ['option' => trans('backend.commons.shop_type')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.shop_types'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.shop-types.index'),
                ], [
                    'name' => trans('backend.commons.show_action', ['option' => trans('backend.commons.shop_type')]),
                    'icon' => 'fa fa-eye',
                    'link' => route('backend.shop-types.show', $shopType->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.shop-types.create'),
                ], [
                    'name' => trans('backend.commons.edit'),
                    'icon' => 'fa fa-edit',
                    'class' => 'btn btn-warning',
                    'link' => route('backend.shop-types.edit', $shopType->id),
                ],
            ],
        ];
        return view('backend::shop-types.create_and_edit_show', compact('shopType', 'pages'));
    }

    public function create(ShopType $shopType)
    {
        $pages = [
            'title' => trans('backend.commons.add_action', ['option' => trans('backend.commons.shop_type')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.shop_types'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.shop-types.index'),
                ], [
                    'name' => trans('backend.commons.add_action', ['option' => trans('backend.commons.shop_type')]),
                    'icon' => 'fa fa-plus',
                    'link' => route('backend.shop-types.create'),
                    'active' => true,
                ],
            ],
        ];
        return view('backend::shop-types.create_and_edit_show', compact('pages', 'shopType'));
    }

    public function store(ShopTypeRequest $request)
    {
        $shopType = ShopType::create($request->all());
        return redirect()->route('backend.shop-types.show', $shopType->id)->with('message', trans('backend.messages.created_success'));
    }

    public function edit(ShopType $shopType)
    {
        $shopType or abort(404);
        $pages = [
            'title' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.shop_type')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.shop_types'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.shop-types.index'),
                    'active' => true,
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.shop_type')]),
                    'icon' => 'fa fa-edit',
                    'link' => route('backend.shop-types.edit', $shopType->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.shop-types.create'),
                ], [
                    'name' => trans('backend.commons.look'),
                    'icon' => 'fa fa-eye',
                    'class' => 'btn btn-info',
                    'link' => route('backend.shop-types.show', $shopType->id),
                ],
            ],
        ];
        return view('backend::shop-types.create_and_edit_show', compact('shopType', 'pages'));
    }

    public function update(ShopTypeRequest $request, ShopType $shopType)
    {
        $shopType or abort(404);
        $shopType->update($request->all());
        return redirect()->route('backend.shop-types.show', $shopType->id)->with('message', trans('backend.messages.updated_success'));
    }

    public function destroy($id)
    {
        ShopType::destroy(explode(',', $id)) or abort(404);
        if (request()->ajax()) {
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        return redirect()->route('backend.shop-types.index')->with('message', trans('backend.messages.deleted_success'));
    }
}
