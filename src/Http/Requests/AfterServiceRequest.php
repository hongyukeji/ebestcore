<?php

namespace System\Http\Requests;

use System\Http\Requests\Request;

class AfterServiceRequest extends Request
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
                $route = $this->route('after_service');
                $id = $route ? $route->id : null;
                return [
                    'name' => 'required|string|max:255|unique:after_services,name,' . $id,
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
            'name' => '售后服务名称',
        ];
    }
}
