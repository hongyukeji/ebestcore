<?php

namespace System\Http\Requests;

use System\Http\Requests\Request;

class UserAccountRequest extends Request
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
                    'freeze_money' => 'sometimes|required|numeric|max:999999999999',
                    'spend_money' => 'sometimes|required|numeric|max:999999999999',
                    'point' => 'sometimes|required|numeric|max:999999999999999999',
                    'freeze_point' => 'sometimes|required|numeric|max:999999999999999999',
                    'use_point' => 'sometimes|required|numeric|max:999999999999999999',
                    'history_point' => 'sometimes|required|numeric|max:999999999999999999',
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
            'freeze_money' => trans('backend.commons.freeze_money'),
            'spend_money' => trans('backend.commons.spend_money'),
            'point' => trans('backend.commons.point'),
            'freeze_point' => trans('backend.commons.freeze_point'),
            'use_point' => trans('backend.commons.use_point'),
            'history_point' => trans('backend.commons.history_point'),
        ];
    }
}
