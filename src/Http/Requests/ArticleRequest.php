<?php

namespace System\Http\Requests;

use System\Http\Requests\Request;

class ArticleRequest extends Request
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
                    'title' => 'required|string|max:255',
                    'status' => 'sometimes|integer|nullable',
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
            'title' => '文章标题',
        ];
    }
}
