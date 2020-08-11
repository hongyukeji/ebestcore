<?php

namespace System\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use System\Services\ProductService;

class ProductResource extends JsonResource
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
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'shop_id' => $this->shop_id,
            'name' => $this->name,
            'description' => $this->description,
            'content' => (new ProductService())->formatImageUrl($this->content),
            'mobile_content' => (new ProductService())->formatImageUrl($this->mobile_content),
            'spu_code' => $this->spu_code,
            'image_url' => $this->image_url,
            'images_url' => $this->images_url,
            'video_url' => $this->video_url,
            'price' => $this->price,
            'price_range' => $this->price_range,
            'market_price' => $this->market_price,
            'stock' => $this->stock,
            'is_best' => $this->is_best,
            'is_hot' => $this->is_hot,
            'is_new' => $this->is_new,
            'sale_count' => $this->sale_count,
            'browse_count' => $this->browse_count,
            'comment_count' => $this->comment_count,
            'favorite_count' => $this->favorite_count,
            'status' => $this->status ?? 0,
            'status_format' => $this->status_format,
            'created_at' => (string)$this->created_at,
            'updated_at' => (string)$this->updated_at,
            'skus' => $this->skus ? ProductSkuResource::collection($this->skus) : null,
            'sku_list' => $this->sku_list,
            'specifications' => $this->specifications,
            'after_services' => $this->after_services ? AfterServiceResource::collection($this->after_services) : null,
            'extend' => [
                'after_service' => $this->extend->after_service ?? '',
                'packing_list' => $this->extend->packing_list ?? '',
            ],
            'comments' => ProductCommentResource::collection($this->comments),
        ];
    }
}
