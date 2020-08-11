<?php

namespace System\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'order_id' => $this->order_id,
            'shop_id' => $this->shop_id,
            'shop_name' => $this->shop_name,
            'product' => $this->product,
            'product_sku' => $this->product_sku,
            'product_id' => $this->product_id,
            'product_name' => $this->product_name,
            'product_sku_id' => $this->product_name,
            'product_sku_name' => $this->product_sku_name,
            'product_price' => $this->product_price,
            'product_image' => $this->product_image,
            'product_image_url' => $this->product_image_url,
            'number' => $this->number,
            'subtotal' => $this->subtotal,
            'discount_rate' => $this->discount_rate,
            'discount_amount' => $this->discount_amount,
            'distribution_money' => $this->distribution_money,
            'settlement_amount' => $this->settlement_amount,
            'created_at' => (string)$this->created_at,
            'updated_at' => (string)$this->updated_at,
        ];
    }
}
