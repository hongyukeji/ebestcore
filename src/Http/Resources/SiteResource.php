<?php

namespace System\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SiteResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);
        $data['site_favicon'] = !empty($this['site_favicon']) ? asset_url($this['site_favicon']) : null;
        $data['site_logo'] = !empty($this['site_logo']) ? asset_url($this['site_logo']) : null;
        $data['site_logo_rectangle'] = !empty($this['site_logo_rectangle']) ? asset_url($this['site_logo_rectangle']) : null;
        $data['site_mobile_logo'] = !empty($this['site_mobile_logo']) ? asset_url($this['site_mobile_logo']) : null;
        return $data;
    }
}
