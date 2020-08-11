<?php

namespace System\Http\Requests;

use System\Http\Requests\Request;
use System\Rules\MobileVerifyCodeRule;

class UserRegisterRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [];

        if (in_array('name', config('params.register.register_columns')) !== false) {
            $rules['name'] = 'required|string|max:255|unique:users';
        }

        if (in_array('email', config('params.register.register_columns')) !== false) {
            $rules['email'] = 'sometimes|nullable|string|email|max:255|unique:users';
        }

        if (in_array('mobile', config('params.register.register_columns')) !== false) {
            $rules['mobile'] = 'required|mobile|unique:users';
        }

        if (in_array('password', config('params.register.register_columns')) !== false) {
            $rules['password'] = 'required|string|min:6';
        }

        if (in_array('password_confirmation', config('params.register.register_columns')) !== false) {
            $rules['password_confirmation'] = 'sometimes|string|same:password';
        }

        if (in_array('captcha', config('params.register.register_columns')) !== false) {
            $rules['captcha'] = 'sometimes|captcha';
        }

        if (in_array('agreement', config('params.register.register_columns')) !== false) {
            $rules['agreement'] = 'required|accepted';
        }

        if (in_array('mobile_verify_code', config('params.register.register_columns')) !== false) {
            $rules['mobile_verify_code'] = [
                'required', 'string',
                new MobileVerifyCodeRule(request()->input('mobile')),
            ];
        }

        switch ($this->method()) {
            // CREATE
            case 'POST':
                // UPDATE
            case 'PUT':
            case 'PATCH':
            {
                return $rules;
            }
            case 'GET':
            case 'DELETE':
            default:
                {
                    return $rules;
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
            'name' => '用户名',
            'email' => '邮箱',
            'mobile' => '手机号',
            'password' => '密码',
            'password_confirmation' => '确认密码',
            'captcha' => '验证码',
            'agreement' => '注册协议',
            'mobile_verify_code' => '短信验证码',
        ];
    }
}
