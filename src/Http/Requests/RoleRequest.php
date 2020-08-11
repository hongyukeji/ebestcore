<?php

namespace System\Http\Requests;

use System\Http\Requests\Request;

class RoleRequest extends Request
{
    public function rules()
    {
        switch ($this->method()) {
            // CREATE
            case 'POST':
                // UPDATE
            case 'PUT':
            case 'PATCH':
            {
                $role = $this->route('role');
                $id = $role ? $role->id : null;
                return [
                    'name' => 'required|string|max:255|unique:roles,name,' . $id,
                    'guard_name' => 'required|string|nullable|max:255',
                    'group' => 'sometimes|string|nullable|max:255',
                    'title' => 'sometimes|string|nullable|max:255',
                    'description' => 'sometimes|string|nullable|max:255',
                    'sort' => 'sometimes|integer|nullable',
                    'status' => 'sometimes|boolean|nullable',
                ];
            }
            case 'GET':
            case 'DELETE':
            default:
                {
                    return [];
                };
        }
    }

    public function messages()
    {
        return [
            'name.regex' => trans('backend.messages.role_name_tip'),
        ];
    }

    public function attributes()
    {
        return [
            'name' => '角色名称',
            'guard_name' => '守卫名称',
            'group' => '角色组',
            'title' => '角色标题',
            'description' => '角色描述',
            'sort' => '排序',
            'status' => '状态',
        ];
    }
}
