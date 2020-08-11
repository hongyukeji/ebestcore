<?php

namespace System\Http\Requests;

use System\Http\Requests\Request;

class ShopGradeRequest extends Request
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
                $id = $this->route('shop_grade') ? $this->route('shop_grade')->id : null;
                return [
                    'name' => 'required|string|nullable|max:255|unique:shop_grades,name,' . $id,
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
            'name' => '店铺等级名称',
        ];
    }
}
