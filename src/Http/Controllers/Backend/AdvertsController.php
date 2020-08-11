<?php

namespace System\Http\Controllers\Backend;

use System\Models\Advert;
use System\Http\Requests\AdvertRequest;
use Illuminate\Http\Request;

class AdvertsController extends Controller
{
    public function index(Request $request)
    {
        $pages = [
            'title' => trans('backend.commons.adverts'),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.adverts'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.adverts.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.adverts.create'),
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
                    'link' => route('backend.adverts.index'),
                ],
            ],
        ];

        $filters = [];
        $builder = Advert::query();

        // Search 参数用来模糊搜索数据
        if ($request->filled('search')) {
            $search = $request->input('search');
            $filters['search'] = $search;
            $like = "%{$search}%";
            $builder->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhere('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhere('group', 'like', $like);
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

        return view('backend::adverts.index', compact('items', 'pages'));
    }

    public function show(Advert $advert)
    {
        $advert or abort(404);
        $pages = [
            'title' => trans('backend.commons.show_action', ['option' => trans('backend.commons.advert')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.adverts'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.adverts.index'),
                ], [
                    'name' => trans('backend.commons.show_action', ['option' => trans('backend.commons.advert')]),
                    'icon' => 'fa fa-eye',
                    'link' => route('backend.adverts.show', $advert->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.advert')]),
                    'icon' => 'fa fa-edit',
                    'class' => 'btn btn-warning',
                    'link' => route('backend.adverts.edit', $advert->id),
                ],
            ],
        ];
        return view('backend::adverts.show', compact('advert', 'pages'));
    }

    public function create(Advert $advert)
    {
        $pages = [
            'title' => trans('backend.commons.add_action', ['option' => trans('backend.commons.advert')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.adverts'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.adverts.index'),
                ], [
                    'name' => trans('backend.commons.add_action', ['option' => trans('backend.commons.advert')]),
                    'icon' => 'fa fa-plus',
                    'link' => route('backend.adverts.create'),
                    'active' => true,
                ],
            ],
        ];
        return view('backend::adverts.create_and_edit', compact('pages', 'advert'));
    }

    public function store(AdvertRequest $request)
    {
        $advert = Advert::create($request->all());
        return redirect()->route('backend.adverts.show', $advert->id)->with('message', trans('backend.messages.created_success'));
    }

    public function edit(Advert $advert)
    {
        $advert or abort(404);
        $pages = [
            'title' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.advert')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.adverts'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.adverts.index'),
                    'active' => true,
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.advert')]),
                    'icon' => 'fa fa-edit',
                    'link' => route('backend.adverts.edit', $advert->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.look'),
                    'icon' => 'fa fa-eye',
                    'class' => 'btn btn-info',
                    'link' => route('backend.adverts.show', $advert->id),
                ],
            ],
        ];
        return view('backend::adverts.create_and_edit', compact('advert', 'pages'));
    }

    public function update(AdvertRequest $request, Advert $advert)
    {
        $advert or abort(404);
        $advert->update($request->all());
        return redirect()->route('backend.adverts.show', $advert->id)->with('message', trans('backend.messages.updated_success'));
    }

    public function destroy($id)
    {
        Advert::destroy(explode(',', $id)) or abort(404);
        if (request()->ajax()) {
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        return redirect()->route('backend.adverts.index')->with('message', trans('backend.messages.deleted_success'));
    }
}
