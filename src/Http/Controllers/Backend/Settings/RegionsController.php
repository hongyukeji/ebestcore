<?php

namespace System\Http\Controllers\Backend\Settings;

use System\Http\Controllers\Backend\Controller;
use System\Models\Category;
use System\Models\Region;
use Illuminate\Support\Facades\DB;
use System\Http\Requests\RegionRequest;
use Illuminate\Http\Request;

class RegionsController extends Controller
{
    public function index(Request $request)
    {
        $pages = [
            'title' => trans('backend.commons.regions'),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.global'),
                    'icon' => 'fa fa-cog',
                    'link' => '',
                ], [
                    'name' => trans('backend.commons.regions'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.settings.regions.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.settings.regions.create'),
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
                    'link' => route('backend.settings.regions.index'),
                ],
            ],
        ];

        $filters = [];
        $builder = Region::query();

        // Search 参数用来模糊搜索数据
        if ($request->filled('search')) {
            $search = $request->input('search');
            $filters['search'] = $search;
            $like = "%{$search}%";
            $builder->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhere('name_en', 'like', $like)
                    ->orWhere('area_code', 'like', $like)
                    ->orWhere('standard_code', 'like', $like)
                    ->orWhere('postcode', 'like', $like);
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
        $regions = Region::query()->orderBy('sort', 'desc')->get([
            'id', 'name', 'parent_id', 'sort', DB::raw('name as text')
        ]);
        return view('backend::settings.regions.index', compact('items', 'regions'));
    }

    public function show(Region $region)
    {
        $region or abort(404);
        $pages = [
            'title' => trans('backend.commons.show_action', ['option' => trans('backend.commons.region')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.regions'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.settings.regions.index'),
                ], [
                    'name' => trans('backend.commons.show_action', ['option' => trans('backend.commons.region')]),
                    'icon' => 'fa fa-eye',
                    'link' => route('backend.settings.regions.show', $region->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.region')]),
                    'icon' => 'fa fa-edit',
                    'class' => 'btn btn-warning',
                    'link' => route('backend.settings.regions.edit', $region->id),
                ],
            ],
        ];
        return view('backend::settings.regions.show', compact('region', 'pages'));
    }

    public function create(Region $region)
    {
        $pages = [
            'title' => trans('backend.commons.add_action', ['option' => trans('backend.commons.region')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.regions'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.settings.regions.index'),
                ], [
                    'name' => trans('backend.commons.add_action', ['option' => trans('backend.commons.region')]),
                    'icon' => 'fa fa-plus',
                    'link' => route('backend.settings.regions.create'),
                    'active' => true,
                ],
            ],
        ];
        return view('backend::settings.regions.create_and_edit', compact('pages', 'region'));
    }

    public function store(RegionRequest $request)
    {
        $region = Region::create($request->all());
        return redirect()->route('backend.settings.regions.show', $region->id)->with('message', trans('backend.messages.created_success'));
    }

    public function edit(Region $region)
    {
        $region or abort(404);
        $pages = [
            'title' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.region')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.regions'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.settings.regions.index'),
                    'active' => true,
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.region')]),
                    'icon' => 'fa fa-edit',
                    'link' => route('backend.settings.regions.edit', $region->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.look'),
                    'icon' => 'fa fa-eye',
                    'class' => 'btn btn-info',
                    'link' => route('backend.settings.regions.show', $region->id),
                ],
            ],
        ];
        return view('backend::settings.regions.create_and_edit', compact('region', 'pages'));
    }

    public function update(RegionRequest $request, Region $region)
    {
        $region or abort(404);
        $region->update($request->all());
        return redirect()->route('backend.settings.regions.show', $region->id)->with('message', trans('backend.messages.updated_success'));
    }

    public function destroy($id)
    {
        Region::destroy(explode(',', $id)) or abort(404);
        if (request()->ajax()) {
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        return redirect()->route('backend.settings.regions.index')->with('message', trans('backend.messages.deleted_success'));
    }
}
