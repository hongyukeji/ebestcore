<?php

namespace System\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
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
            'uri' => $this->uri,
            'permission' => $this->permission,
            'icon' => $this->icon,
            'label' => $this->label,
            'target' => $this->target,
            'parent_id' => $this->parent_id,
            'sort' => $this->sort,
            'status' => $this->status,
            'created_at' => (string)$this->created_at,
            'updated_at' => (string)$this->updated_at,
        ];
    }
}
