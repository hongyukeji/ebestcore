<?php

namespace System\Http\Requests;

use System\Http\Requests\Request;

class UserRequest extends Request
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
                $route = $this->route('user');
                $id = $route ? $route->id : null;
                return [
                    'name' => 'sometimes|string|nullable|max:255|unique:users,name,' . $id,
                    'mobile' => 'sometimes|mobile|nullable|max:255|unique:users,mobile,' . $id,
                    'email' => 'sometimes|email|nullable|max:255|unique:users,email,' . $id,
                    //'avatar' => 'sometimes|string|nullable|max:255',
                    'password' => 'sometimes|string|nullable|min:6|max:255',
                    'status' => 'sometimes|integer|nullable',
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
            'name' => '用户名',
        ];
    }
}
