<?php

namespace System\Http\Requests;

use System\Http\Requests\Request;
use System\Models\UserInvoice;

class UserInvoiceRequest extends Request
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
                    'open_type' => 'filled|numeric',
                    'invoice_rise' => 'filled|string|max:255',
                    'invoice_type' => 'sometimes|numeric',
                    'registration_no' => 'sometimes|string|nullable|max:255',
                    'bank_name' => 'sometimes|string|nullable|max:255',
                    'account' => 'sometimes|string|nullable|max:255',
                    'address' => 'sometimes|string|nullable|max:255',
                    'telephone' => 'sometimes|string|nullable|max:255',
                    'is_default' => 'sometimes|nullable|boolean',
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
            'open_type' => '开具类型',
            'invoice_rise' => '发票抬头',
            'invoice_type' => '发票类型',
            'registration_no' => '税务登记证号',
            'bank_name' => '开户银行名称',
            'account' => '基本开户账号',
            'address' => '注册场所地址',
            'telephone' => '注册固定电话',
            'is_default' => '默认发票',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (UserInvoice::query()->where('user_id', auth()->id())->count() > config('params.users.max_invoice_number', 5)) {
                $validator->errors()->add('field', '超出最大发票数量，请删除或修改现有发票');
            }
        });
    }
}
