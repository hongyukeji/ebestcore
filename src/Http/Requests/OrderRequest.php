<?php

namespace System\Http\Requests;

use System\Http\Requests\Request;

class OrderRequest extends Request
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
                //$id = $this->route('order') ? $this->route('order')->id : null;
                return [
                    //'name' => 'required|string|nullable|max:255|unique:orders,name,' . $id,
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
            //'name' => '名称',
        ];
    }
}
