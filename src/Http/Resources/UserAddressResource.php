<?php

namespace System\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserAddressResource extends JsonResource
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
            'consignee' => $this->consignee ?? '',
            'mobile' => $this->mobile ?? '',
            'phone' => $this->phone ?? '',
            'email' => $this->email ?? '',
            'province' => $this->province ?? '',
            'city' => $this->city ?? '',
            'district' => $this->district ?? '',
            'address' => $this->address ?? '',
            'postal_code' => $this->postal_code ?? '',
            'is_default' => $this->is_default,
            'address_format' => $this->address_format ?? '',
            'created_at' => (string)$this->created_at,
            'updated_at' => (string)$this->updated_at,
        ];
    }
}
