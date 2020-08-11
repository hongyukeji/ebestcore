<?php

namespace System\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'mobile' => $this->mobile,
            'email_verified_at' => $this->email_verified_at,
            'mobile_verified_at' => $this->mobile_verified_at,
            'avatar' => $this->avatar,
            'avatar_url' => $this->avatar_url,
            'created_at' => (string)$this['created_at'] ?? now(),
            'updated_at' => (string)$this['updated_at'] ?? now(),
            'grade' => $this->grade,
            'total_count' => $this->total_count,
            'account' => new UserAccountResource($this->account),
            'extend' => new UserExtendResource($this->extend),
        ];
    }
}
