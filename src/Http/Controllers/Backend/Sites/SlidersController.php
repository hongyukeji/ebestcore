<?php

namespace System\Http\Controllers\Backend\Sites;

use System\Http\Controllers\Backend\Controller;
use System\Models\Model;
use System\Models\Slider;
use System\Http\Requests\SliderRequest;
use Illuminate\Http\Request;

class SlidersController extends Controller
{
    public function index(Request $request)
    {
        $pages = [
            'title' => trans('backend.commons.sliders'),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.sliders'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.sites.sliders.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.sites.sliders.create'),
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
                    'link' => route('backend.sites.sliders.index'),
                ],
            ],
        ];

        $filters = [];
        $builder = Slider::query();

        // Search 参数用来模糊搜索数据
        if ($request->filled('search')) {
            $search = $request->input('search');
            $filters['search'] = $search;
            $like = "%{$search}%";
            $builder->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)
                    ->orWhere('name', 'like', $like)
                    ->orWhere('link', 'like', $like)
                    ->orWhere('description', 'like', $like);
            });
        }

        if ($request->filled('group')) {
            $builder->where('group', $request->input('group'));
        }

        if ($request->filled('status')) {
            $builder->where('status', $request->input('status'));
        }

        // 排序
        $sort_key = $request->input('order_by_column', 'created_at');
        $sort_value = $request->input('order_by_direction', 'desc');
        $filters['order_by_column'] = $sort_key;
        $filters['order_by_direction'] = $sort_value;
        $builder->orderBy($sort_key, $sort_value);

        // 分页
        $per_page = $request->input('per_page', config('params.pages.per_page'));
        $filters['per_page'] = $per_page;
        $items = $builder->paginate($per_page)->appends($filters);

        return view('backend::sites.sliders.index', compact('items', 'pages'));
    }

    public function show(Slider $slider)
    {
        $slider or abort(404);
        $pages = [
            'title' => trans('backend.commons.show_action', ['option' => trans('backend.commons.slider')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.sliders'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.sites.sliders.index'),
                ], [
                    'name' => trans('backend.commons.show_action', ['option' => trans('backend.commons.slider')]),
                    'icon' => 'fa fa-eye',
                    'link' => route('backend.sites.sliders.show', $slider->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.slider')]),
                    'icon' => 'fa fa-edit',
                    'class' => 'btn btn-warning',
                    'link' => route('backend.sites.sliders.edit', $slider->id),
                ],
            ],
        ];
        return view('backend::sites.sliders.show', compact('slider', 'pages'));
    }

    public function create(Slider $slider)
    {
        $pages = [
            'title' => trans('backend.commons.add_action', ['option' => trans('backend.commons.slider')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.sliders'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.sites.sliders.index'),
                ], [
                    'name' => trans('backend.commons.add_action', ['option' => trans('backend.commons.slider')]),
                    'icon' => 'fa fa-plus',
                    'link' => route('backend.sites.sliders.create'),
                    'active' => true,
                ],
            ],
        ];
        return view('backend::sites.sliders.create_and_edit', compact('pages', 'slider'));
    }

    public function store(SliderRequest $request)
    {
        $slider = Slider::create($request->all());
        return redirect()->route('backend.sites.sliders.show', $slider->id)->with('message', trans('backend.messages.created_success'));
    }

    public function edit(Slider $slider)
    {
        $slider or abort(404);
        $pages = [
            'title' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.slider')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.sliders'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.sites.sliders.index'),
                    'active' => true,
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.slider')]),
                    'icon' => 'fa fa-edit',
                    'link' => route('backend.sites.sliders.edit', $slider->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.look'),
                    'icon' => 'fa fa-eye',
                    'class' => 'btn btn-info',
                    'link' => route('backend.sites.sliders.show', $slider->id),
                ],
            ],
        ];
        return view('backend::sites.sliders.create_and_edit', compact('slider', 'pages'));
    }

    public function update(SliderRequest $request, Slider $slider)
    {
        $slider or abort(404);
        $slider->update($request->all());
        return redirect()->route('backend.sites.sliders.show', $slider->id)->with('message', trans('backend.messages.updated_success'));
    }

    public function destroy($id)
    {
        Slider::destroy(explode(',', $id)) or abort(404);
        if (request()->ajax()) {
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        return redirect()->route('backend.sites.sliders.index')->with('message', trans('backend.messages.deleted_success'));
    }
}
