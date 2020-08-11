<?php

namespace System\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserExtendResource extends JsonResource
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
            'nickname' => $this->nickname,
            'qq' => $this->qq,
            'ww' => $this->ww,
            'age' => $this->age,
            'sex' => $this->sex,
            'birthday' => $this->birthday,
            'created_at' => (string)$this->created_at,
            'updated_at' => (string)$this->updated_at,
        ];
    }
}
