<?php

namespace System\Http\Controllers\Backend;

use System\Models\AfterService;
use System\Http\Controllers\Backend\Controller;
use System\Http\Requests\AfterServiceRequest;
use Illuminate\Http\Request;

class AfterServicesController extends Controller
{
    public function index(Request $request)
    {
        $pages = [
            'title' => trans('backend.commons.after_services'),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.after_services'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.after-services.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.after-services.create'),
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
                    'link' => route('backend.after-services.index'),
                ],
            ],
        ];

        $filters = [];
        $builder = AfterService::query();

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

        return view('backend::after-services.index', compact('items', 'pages'));
    }

    public function show(AfterService $afterService)
    {
        $afterService or abort(404);
        $pages = [
            'title' => trans('backend.commons.show_action', ['option' => trans('backend.commons.after_service')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.after_services'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.after-services.index'),
                ], [
                    'name' => trans('backend.commons.show_action', ['option' => trans('backend.commons.after_service')]),
                    'icon' => 'fa fa-eye',
                    'link' => route('backend.after-services.show', $afterService->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.after-services.create'),
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.after_service')]),
                    'icon' => 'fa fa-edit',
                    'class' => 'btn btn-warning',
                    'link' => route('backend.after-services.edit', $afterService->id),
                ],
            ],
        ];
        return view('backend::after-services.show', compact('afterService', 'pages'));
    }

    public function create(AfterService $afterService)
    {
        $pages = [
            'title' => trans('backend.commons.add_action', ['option' => trans('backend.commons.after_service')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.after_services'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.after-services.index'),
                ], [
                    'name' => trans('backend.commons.add_action', ['option' => trans('backend.commons.after_service')]),
                    'icon' => 'fa fa-plus',
                    'link' => route('backend.after-services.create'),
                    'active' => true,
                ],
            ],
        ];
        return view('backend::after-services.create_and_edit', compact('pages', 'afterService'));
    }

    public function store(AfterServiceRequest $request)
    {
        $afterService = AfterService::create($request->all());
        return redirect()->route('backend.after-services.show', $afterService->id)->with('message', trans('backend.messages.created_success'));
    }

    public function edit(AfterService $afterService)
    {
        $afterService or abort(404);
        $pages = [
            'title' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.after_service')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.after_services'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.after-services.index'),
                    'active' => true,
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.after_service')]),
                    'icon' => 'fa fa-edit',
                    'link' => route('backend.after-services.edit', $afterService->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.look'),
                    'icon' => 'fa fa-eye',
                    'class' => 'btn btn-info',
                    'link' => route('backend.after-services.show', $afterService->id),
                ],
            ],
        ];
        return view('backend::after-services.create_and_edit', compact('afterService', 'pages'));
    }

    public function update(AfterServiceRequest $request, AfterService $afterService)
    {
        $afterService or abort(404);
        $afterService->update($request->all());
        return redirect()->route('backend.after-services.show', $afterService->id)->with('message', trans('backend.messages.updated_success'));
    }

    public function destroy($id)
    {
        AfterService::destroy(explode(',', $id)) or abort(404);
        if (request()->ajax()) {
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        return redirect()->route('backend.after-services.index')->with('message', trans('backend.messages.deleted_success'));
    }
}
