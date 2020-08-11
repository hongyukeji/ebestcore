<?php

namespace System\Http\Requests;

use System\Http\Requests\Request;

class ProductSpecificationRequest extends Request
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
                $id = $this->route('specification') ? $this->route('specification') : null;
                return [
                    'name' => 'required|string|max:255|unique:product_specifications,name,' . $id,
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
            'spec_name' => '规格名称',
            'spec_option.*.spec_option_name' => '规格选项名称',
            'spec_option.*.sort' => '规格选项排序',
        ];
    }
}
