<?php

namespace System\Http\Requests;

use System\Http\Requests\Request;

class PermissionRequest extends Request
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
                $permission = $this->route('permission');
                $id = $permission ? $permission->id : null;
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
            // Validation messages
        ];
    }

    public function attributes()
    {
        return [
            'name' => '权限名称',
            'guard_name' => '守卫名称',
            'group' => '权限组',
            'title' => '权限标题',
            'description' => '权限描述',
            'sort' => '排序',
            'status' => '状态',
        ];
    }
}
