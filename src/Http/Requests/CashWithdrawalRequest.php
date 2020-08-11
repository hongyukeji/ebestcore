<?php

namespace System\Http\Requests;

use System\Http\Requests\Request;

class CashWithdrawalRequest extends Request
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
                    'money' => 'sometimes|required|numeric|max:999999999999',
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
            'money' => trans('backend.commons.money'),
        ];
    }
}
