<?php

namespace System\Http\Controllers\Backend\User;

use System\Models\UserGrade;
use System\Http\Requests\UserGradeRequest;
use Illuminate\Http\Request;
use System\Http\Controllers\Backend\Controller;

class GradesController extends Controller
{
    public function index(Request $request)
    {
        $pages = [
            'title' => trans('backend.commons.user_grade'),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.member_center'),
                    'icon' => 'fa fa-user',
                    'link' => route('backend.users.index'),
                ], [
                    'name' => trans('backend.commons.user_grade'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.user.grades.index'),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.add'),
                    'icon' => 'fa fa-plus',
                    'class' => 'btn btn-primary',
                    'link' => route('backend.user.grades.create'),
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
                    'link' => route('backend.user.grades.index'),
                ],
            ],
        ];

        $filters = [];
        $builder = UserGrade::query();

        // Search 参数用来模糊搜索数据
        if ($request->filled('search')) {
            $search = $request->input('search');
            $filters['search'] = $search;
            $like = "%{$search}%";
            $builder->where(function ($query) use ($like) {
                $query->where('name', 'like', $like)
                    ->orWhere('start_point', 'like', $like)
                    ->orWhere('end_point', 'like', $like)
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
        $filters['per_page'] = $per_page;
        $items = $builder->paginate($per_page)->appends($filters);

        return view('backend::user.grades.index', compact('items', 'pages'));
    }

    public function show(UserGrade $grade)
    {
        $userGrade = $grade;
        $userGrade or abort(404);
        $pages = [
            'title' => trans('backend.commons.show_action', ['option' => trans('backend.commons.user_grade')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.user_grade'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.user.grades.index'),
                ], [
                    'name' => trans('backend.commons.show_action', ['option' => trans('backend.commons.user_grade')]),
                    'icon' => 'fa fa-eye',
                    'link' => route('backend.user.grades.show', $userGrade->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.user_grade')]),
                    'icon' => 'fa fa-edit',
                    'class' => 'btn btn-warning',
                    'link' => route('backend.user.grades.edit', $userGrade->id),
                ],
            ],
        ];
        return view('backend::user.grades.show', compact('userGrade', 'pages'));
    }

    public function create(UserGrade $userGrade)
    {
        $pages = [
            'title' => trans('backend.commons.add_action', ['option' => trans('backend.commons.user_grade')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.user_grade'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.user.grades.index'),
                ], [
                    'name' => trans('backend.commons.add_action', ['option' => trans('backend.commons.user_grade')]),
                    'icon' => 'fa fa-plus',
                    'link' => route('backend.user.grades.create'),
                    'active' => true,
                ],
            ],
        ];
        return view('backend::user.grades.create_and_edit', compact('pages', 'userGrade'));
    }

    public function store(UserGradeRequest $request)
    {
        $userGrade = UserGrade::create($request->all());
        return redirect()->route('backend.user.grades.show', $userGrade->id)->with('message', trans('backend.messages.created_success'));
    }

    public function edit(UserGrade $grade)
    {
        $userGrade = $grade;
        $userGrade or abort(404);
        $pages = [
            'title' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.user_grade')]),
            'description' => '',
            'breadcrumbs' => [
                [
                    'name' => trans('backend.commons.home'),
                    'icon' => 'fa fa-home',
                    'link' => route('backend.index'),
                ], [
                    'name' => trans('backend.commons.user_grade'),
                    'icon' => 'fa fa-circle-o',
                    'link' => route('backend.user.grades.index'),
                    'active' => true,
                ], [
                    'name' => trans('backend.commons.edit_action', ['option' => trans('backend.commons.user_grade')]),
                    'icon' => 'fa fa-edit',
                    'link' => route('backend.user.grades.edit', $userGrade->id),
                    'active' => true,
                ],
            ],
            'button_group' => [
                [
                    'name' => trans('backend.commons.look'),
                    'icon' => 'fa fa-eye',
                    'class' => 'btn btn-info',
                    'link' => route('backend.user.grades.show', $userGrade->id),
                ],
            ],
        ];
        return view('backend::user.grades.create_and_edit', compact('userGrade', 'pages'));
    }

    public function update(UserGradeRequest $request, UserGrade $grade)
    {
        $userGrade = $grade;
        $userGrade or abort(404);
        $userGrade->update($request->all());
        return redirect()->route('backend.user.grades.show', $userGrade->id)->with('message', trans('backend.messages.updated_success'));
    }

    public function destroy($id)
    {
        UserGrade::destroy(explode(',', $id)) or abort(404);
        if (request()->ajax()) {
            return response()->json(api_result(0), 200)->setEncodingOptions(JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_NUMERIC_CHECK);
        }
        return redirect()->route('backend.users.users.user-grades.index')->with('message', trans('backend.messages.deleted_success'));
    }
}
