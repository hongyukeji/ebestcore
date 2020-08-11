<?php

namespace System\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductCommentResource extends JsonResource
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
            'user_id' => $this->user_id,
            'product_id' => $this->product_id,
            'product_sku_id' => $this->product_sku_id,
            'product_sku_name' => $this->product_sku_name,
            'score' => $this->score,
            'content' => $this->content,
            'append_content' => $this->append_content,
            'status_format' => $this->status_format,
            'created_at' => (string)$this->created_at,
            'updated_at' => (string)$this->updated_at,
            'user' => $this->user,
            'order' => $this->order,
            'product' => $this->product,
            'product_sku' => $this->productSku,
        ];
    }
}
