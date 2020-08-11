<?php

namespace System\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AfterServiceResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'image' => $this->image,
            'is_lock' => $this->is_lock,
            'image_url' => $this->image ? asset_url($this->image) : '',
            'sort' => $this->sort,
            'status' => $this->status,
            'created_at' => (string)$this['created_at'] ?? now(),
            'updated_at' => (string)$this['updated_at'] ?? now(),
        ];
    }
}
