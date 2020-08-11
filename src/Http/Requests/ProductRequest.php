<?php

namespace System\Http\Requests;

use System\Http\Requests\Request;

class ProductRequest extends Request
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
                $id = $this->route('product') ? $this->route('product')->id : null;
                return [
                    'name' => 'sometimes|required|string|max:255',
                    'spu_code' => 'sometimes|nullable|string|max:255|unique:products,spu_code,' . $id,
                    'price' => 'sometimes|price|nullable',
                    'market_price' => 'sometimes|price|nullable',
                    'cost_price' => 'sometimes|price|nullable',
                    'stock' => 'sometimes|integer|nullable',
                    'sort' => 'sometimes|integer|nullable',
                    'status' => 'sometimes|integer|nullable',
                    'is_best' => 'sometimes|boolean|nullable',
                    'is_hot' => 'sometimes|boolean|nullable',
                    'is_new' => 'sometimes|boolean|nullable',
                    "skus" => 'sometimes|array|nullable',
                    'skus.*.price' => 'sometimes|price|nullable',
                    'skus.*.market_price' => 'sometimes|price|nullable',
                    'skus.*.cost_price' => 'sometimes|price|nullable',
                    'skus.*.stock' => 'sometimes|integer|nullable',
                    'skus.*.sort' => 'sometimes|integer|nullable',
                    'skus.*.sku_code' => 'sometimes|string|nullable|max:255',
                    'skus.*.status' => 'sometimes|boolean|nullable',
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
            'name' => '商品名称',
            'description' => '商品描述',
            'spu_code' => '商品编码',
            'price' => '价格',
            'market_price' => '市场价',
            'cost_price' => '成本价',
            'video' => '商品视频',
            'video_url' => '商品视频链接',
            'stock' => '库存',
            'sort' => '排序',
            'status' => '商品状态',
            'skus' => '商品规格',
            'skus.*.price' => '商品规格 价格',
            'skus.*.market_price' => '商品规格 市场价',
            'skus.*.cost_price' => '商品规格 成本价',
            'skus.*.stock' => '商品规格 库存',
            'skus.*.sort' => '商品规格 排序',
            'skus.*.sku_code' => '商品规格 编码',
            'skus.*.status' => '商品规格 :key 状态',
        ];
    }
}
