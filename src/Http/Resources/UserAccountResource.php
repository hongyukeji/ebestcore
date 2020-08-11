<?php

namespace System\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserAccountResource extends JsonResource
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
            'money' => $this->money,
            'freeze_money' => $this->freeze_money,
            'spend_money' => $this->spend_money,
            'point' => $this->point,
            'freeze_point' => $this->freeze_point,
            'use_point' => $this->use_point,
            'history_point' => $this->history_point,
            'created_at' => (string)$this->created_at,
            'updated_at' => (string)$this->updated_at,
        ];
    }
}
