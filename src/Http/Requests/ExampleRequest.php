<?php

namespace System\Http\Requests;

use System\Http\Requests\Request;

class ExampleRequest extends Request
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
                $id = $this->route('example') ? $this->route('example')->id : null;
                return [
                    'name' => 'required|string|nullable|max:255|unique:examples,name,' . $id,
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
            'name' => '名称',
        ];
    }
}
