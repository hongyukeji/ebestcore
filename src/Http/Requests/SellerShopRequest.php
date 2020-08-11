<?php

namespace System\Http\Requests;

use System\Http\Requests\Request;
use System\Models\Shop;

class SellerShopRequest extends Request
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
                $id = null;
                if (auth()->check()) {
                    $user_id = auth()->id();
                    $shop = Shop::query()->where('user_id', $user_id)->first();
                    if ($shop) {
                        $id = $shop->id;
                    }
                }
                return [
                    'name' => 'required|string|nullable|max:255|unique:shops,name,' . $id,
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
            'name' => '店铺名称',
        ];
    }
}
