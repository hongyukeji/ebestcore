<?php

namespace System\Http\Requests;

use System\Http\Requests\Request;

class PageRequest extends Request
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
                $id = $this->route('page') ? $this->route('page')->id : null;
                return [
                    'title' => 'required|string|nullable|max:255',
                    'slug' => 'sometimes|string|nullable|max:255|unique:pages,slug,' . $id,
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
            'title' => '页面名称',
        ];
    }
}
