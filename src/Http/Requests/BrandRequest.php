<?php

namespace System\Http\Requests;

use System\Http\Requests\Request;

class BrandRequest extends Request
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
                $route = $this->route('brand');
                $id = $route ? $route->id : null;
                return [
                    'name' => 'required|string|max:255|unique:brands,name,' . $id,
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
            'name' => '品牌名称',
        ];
    }
}
