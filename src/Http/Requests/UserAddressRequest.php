<?php

namespace System\Http\Requests;

use System\Models\UserAddress;
use System\Http\Requests\Request;

class UserAddressRequest extends Request
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
                    'consignee' => 'filled|string|max:255',
                    'province' => 'filled|string|max:255',
                    'city' => 'filled|string|max:255',
                    'district' => 'filled|string|max:255',
                    'address' => 'filled|string|max:255',
                    'postal_code' => 'nullable|string',
                    'phone' => 'nullable|string|max:255',
                    'mobile' => 'nullable|string|max:255',
                    'email' => 'nullable|email',
                    'is_default' => 'nullable|boolean',
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
            'consignee' => '收货人',
            'province' => '省',
            'city' => '市',
            'district' => '区',
            'address' => '详细地址',
            'postal_code' => '邮政编码',
            'phone' => '联系电话',
            'mobile' => '手机号',
            'email' => '电子邮箱',
            'is_default' => '默认收货地址',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (UserAddress::query()->where('user_id', auth()->user()->id)->count() > config('params.users.max_address_number', 5)) {
                $validator->errors()->add('field', '超出最大收货地址数量，请删除或修改现有收货地址');
            }
        });
    }
}
