<?php

namespace System\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductImageResource extends JsonResource
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
            'id' => $this['id'] ?? 0,
            'product_id' => $this['product_id'] ?? 0,
            'image_path' => $this['image_path'] ?? '',
            'image_url' => !empty($this['image_path']) ? asset_url($this['image_path']) : '',
            'sort' => $this['sort'] ?? 0,
            //'created_at' => (string)($this['created_at']  ?? now()),
            //'updated_at' => (string)($this['updated_at']  ?? now()),
        ];
    }
}
