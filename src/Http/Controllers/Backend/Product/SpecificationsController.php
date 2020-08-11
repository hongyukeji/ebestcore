<?php

namespace System\Http\Controllers\Backend\Product;

use System\Http\Controllers\Backend\Controller;
use System\Models\ProductSpecification;
use System\Models\ProductSpecification as Specification;
use Illuminate\Http\Request;
use System\Http\Requests\ProductSpecificationRequest;

class SpecificationsController extends Controller
{
    public function index(Request $request)
    {
        $pages = [
            'title' => trans('backend.commons.product_specifications'),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.product_specifications'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.product.specifications.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.product.specifications.create'),
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
                    'link' => route('backend.product.specifications.index'),
                ],
            ],
        ];

        $filters = [];
        $builder = ProductSpecification::query();

        // Search 参数用来模糊搜索数据
        if ($request->filled('search')) {
            $filters['search'] = $request->input('search');
            $search = "%{$filters['search']}%";
            $builder->where(function ($query) use ($search) {
                $query->where('specification_name', 'like', $search)
                    ->orWhere('specification_value', 'like', $search);
            });
        }

        if ($request->filled('status')) {
            $filters['status'] = $request->input('status');
            $builder->where('status', $filters['status']);
        }

        // 排序
        $filters['order_by_column'] = $request->input('order_by_column', 'id');
        $filters['order_by_direction'] = $request->input('order_by_direction', 'desc');
        $builder->orderBy($filters['order_by_column'], $filters['order_by_direction']);

        // 分页
        $filters['per_page'] = $request->input('per_page', config('params.pages.per_page', 15));
        $items = $builder->paginate($filters['per_page'])->appends($filters);

        return view('backend::product.specifications.index', compact('items', 'pages'));
    }

    public function show(Specification $specification)
    {
        $specification or abort(404);
        dd($specification);
    }

    public function create(ProductSpecification $productSpecification)
    {
        $pages = [
            'title' => trans('backend.commons.add_action', ['option' => trans('backend.commons.product_specification')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.product_specifications'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.product.specifications.index'),
                ], [
                    'name' => trans('backend.commons.add_action', ['option' => trans('backend.commons.product_specification')]),
                    'icon' => 'fa fa-plus',
                    'link' => route('backend.product.specifications.create'),
                    'active' => true,
                ],
            ],
        ];
        return view('backend::product.specifications.create_and_edit', compact('pages', 'productSpecification'));
    }

    public function store(ProductSpecificationRequest $request)
    {
        $productSpecification = ProductSpecification::create($request->all());
        return redirect()->route('backend.product.specifications.index')->with('message', trans('backend.messages.created_success'));
    }

    public function edit($id)
    {
        $productSpecification = ProductSpecification::query()->find($id) or abort(404);
        $pages = [
            'title' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.product_specification')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.product_specifications'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.product.specifications.index'),
                    'active' => true,
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.product_specification')]),
                    'icon' => 'fa fa-edit',
                    'link' => route('backend.product.specifications.edit', $productSpecification->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.product.specifications.create'),
                ]
            ],
        ];
        return view('backend::product.specifications.create_and_edit', compact('productSpecification', 'pages'));
    }

    public function update(ProductSpecificationRequest $request, $id)
    {
        $productSpecification = ProductSpecification::query()->find($id) or abort(404);
        $productSpecification->update($request->all());
        return redirect()->route('backend.product.specifications.index')->with('message', trans('backend.messages.updated_success'));
    }

    public function destroy($id)
    {
        ProductSpecification::destroy(explode(',', $id)) or abort(404);
        if (request()->ajax()) {
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        return redirect()->route('backend.product.specifications.index')->with('message', trans('backend.messages.deleted_success'));
    }
}
