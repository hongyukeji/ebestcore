<?php

namespace System\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'order_no' => $this->order_no,
            'order_source' => $this->order_source,
            'total_number' => $this->total_number,
            'total_amount' => $this->total_amount,
            'payment_log_id' => $this->payment_log_id,
            'payment_at' => $this->payment_at,
            'shipping_id' => $this->shipping_id,
            'shipping_fee' => $this->shipping_fee,
            'shipped_at' => $this->shipped_at,
            'received_at' => $this->received_at,
            'express_name' => $this->express_name,
            'express_no' => $this->express_no,
            'refund_status' => $this->refund_status,
            'refund_amount' => $this->refund_amount,
            'comment_status' => $this->comment_status,
            'shop_id' => $this->shop_id,
            'shop_name' => $this->shop_name,
            'user_id' => $this->user_id,
            'user_name' => $this->user_name,
            'consignee_name' => $this->consignee_name,
            'consignee_phone' => $this->consignee_phone,
            'consignee_address' => $this->consignee_address,
            'address' => $this->address,
            'remark' => $this->remark,
            'finished_at' => $this->finished_at,
            'settlement_at' => $this->settlement_at,
            'status' => $this->status,
            'status_format' => $this->status_format,
            'created_at' => (string)$this->created_at,
            'updated_at' => (string)$this->updated_at,
            'details' => OrderDetailResource::collection($this->details),

            'closed_status' => $this->closed_status,
            'status_unpaid' => $this->status_unpaid,
            'status_paid' => $this->status_paid,
            'status_wait_received' => $this->status_wait_received,
            'status_wait_comment' => $this->status_wait_comment,

        ];
    }
}
