<?php

namespace System\Http\Requests;

use System\Http\Requests\Request;

class ProductSpecRequest extends Request
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
                $id = $this->route('spec') ? $this->route('spec')->id : null;
                return [
                    'spec_name' => 'required|string|max:255|unique:product_specs,spec_name,' . $id,
                    'spec_option.*.spec_option_name' => 'sometimes|nullable|string|max:255',
                    'spec_option.*.sort' => 'sometimes|integer|nullable',
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
