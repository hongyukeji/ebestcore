<?php

namespace System\Http\Requests;

use System\Http\Requests\Request;

class ProductTypeRequest extends Request
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
                $id = $this->route('type') ? $this->route('type')->id : null;
                return [
                    'type_name' => 'required|string|nullable|max:255|unique:product_types,type_name,' . $id,
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
            'type_name' => '类型名称',
        ];
    }
}
