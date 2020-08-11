<?php

namespace System\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'user_id' => $this->user_id,
            'shop_id' => $this->shop_id,
            'product_id' => $this->product_id,
            'product_sku_id' => $this->product_sku_id,
            'number' => $this->number,
            'is_selected' => (boolean)$this->is_selected,
            'created_at' => (string)$this['created_at'] ?? now(),
            'updated_at' => (string)$this['updated_at'] ?? now(),
            'price' => $this->price,
            'product' => new ProductResource($this->product),
            'productSku' => new ProductSkuResource($this->productSku),
            'shop' => new ShopResource($this->shop),
        ];
    }
}
