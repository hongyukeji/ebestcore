<?php

namespace System\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use System\Services\CategoryService;

class CategoryResource extends JsonResource
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
            'id' => $this['id'],
            'name' => $this['name'],
            'description' => $this['description'] ?? '',
            'image' => $this['image'],
            'image_url' => $this['image_url'],
            'parent_id' => $this['parent_id'],
            'specification' => (new CategoryService())->getSpecification($this['id']),
            'sort' => $this['sort'] ?? 0,
            'status' => $this['status'],
            'created_at' => (string)$this['created_at'] ?? now(),
            'updated_at' => (string)$this['updated_at'] ?? now(),
        ];
    }
}
