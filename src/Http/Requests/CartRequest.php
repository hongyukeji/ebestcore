<?php

namespace System\Http\Requests;

use System\Http\Requests\Request;

class CartRequest extends Request
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
                return [
                    'product_id' => 'sometimes|integer|nullable|exists:products,id',
                    'product_sku_id' => 'sometimes|integer|nullable',
                    'number' => 'sometimes|integer|nullable',
                    'is_selected' => 'sometimes|boolean|nullable',
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
            'product_id' => '商品ID',
            'product_sku_id' => '商品SkuID',
            'number' => '数量',
            'is_selected' => '选中状态',
        ];
    }
}
