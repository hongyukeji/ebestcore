<?php

namespace System\Http\Controllers\Backend;

use System\Models\Model;
use System\Models\Activity;
use System\Http\Requests\ActivityRequest;
use Illuminate\Http\Request;

class ActivitiesController extends Controller
{
    public function index(Request $request)
    {
        $pages = [
            'title' => trans('backend.commons.activities'),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.activities'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.activities.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.activities.create'),
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
                    'link' => route('backend.activities.index'),
                ],
            ],
        ];

        $filters = [];
        $builder = Activity::query();

        // Search 参数用来模糊搜索数据
        if ($request->filled('search')) {
            $search = $request->input('search');
            $filters['search'] = $search;
            $like = "%{$search}%";
            $builder->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhere('title', 'like', $like)
                    ->orWhere('description', 'like', $like);
            });
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

        return view('backend::activities.index', compact('items', 'pages'));
    }

    public function show(Activity $activity)
    {
        $activity or abort(404);
        $pages = [
            'title' => trans('backend.commons.show_action', ['option' => trans('backend.commons.activity')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.activities'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.activities.index'),
                ], [
                    'name' => trans('backend.commons.show_action', ['option' => trans('backend.commons.activity')]),
                    'icon' => 'fa fa-eye',
                    'link' => route('backend.activities.show', $activity->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.activity')]),
                    'icon' => 'fa fa-edit',
                    'class' => 'btn btn-warning',
                    'link' => route('backend.activities.edit', $activity->id),
                ],
            ],
        ];
        return view('backend::activities.show', compact('activity', 'pages'));
    }

    public function create(Activity $activity)
    {
        $pages = [
            'title' => trans('backend.commons.add_action', ['option' => trans('backend.commons.activity')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.activities'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.activities.index'),
                ], [
                    'name' => trans('backend.commons.add_action', ['option' => trans('backend.commons.activity')]),
                    'icon' => 'fa fa-plus',
                    'link' => route('backend.activities.create'),
                    'active' => true,
                ],
            ],
        ];
        return view('backend::activities.create_and_edit', compact('pages', 'activity'));
    }

    public function store(ActivityRequest $request)
    {
        $activity = Activity::create($request->all());
        return redirect()->route('backend.activities.show', $activity->id)->with('message', trans('backend.messages.created_success'));
    }

    public function edit(Activity $activity)
    {
        $activity or abort(404);
        $pages = [
            'title' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.activity')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.activities'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.activities.index'),
                    'active' => true,
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.activity')]),
                    'icon' => 'fa fa-edit',
                    'link' => route('backend.activities.edit', $activity->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.look'),
                    'icon' => 'fa fa-eye',
                    'class' => 'btn btn-info',
                    'link' => route('backend.activities.show', $activity->id),
                ],
            ],
        ];
        return view('backend::activities.create_and_edit', compact('activity', 'pages'));
    }

    public function update(ActivityRequest $request, Activity $activity)
    {
        $activity or abort(404);
        $activity->update($request->all());
        return redirect()->route('backend.activities.show', $activity->id)->with('message', trans('backend.messages.updated_success'));
    }

    public function destroy($id)
    {
        Activity::destroy(explode(',', $id)) or abort(404);
        if (request()->ajax()) {
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        return redirect()->route('backend.activities.index')->with('message', trans('backend.messages.deleted_success'));
    }
}
