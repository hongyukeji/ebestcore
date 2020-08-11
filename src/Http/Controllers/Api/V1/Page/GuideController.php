<?php

namespace System\Http\Controllers\Api\V1\Page;

use System\Http\Controllers\Api\Controller;
use System\Models\Slider;
use System\Http\Resources\SliderResource;

class GuideController extends Controller
{
    public function index()
    {
        $items = Slider::query()
            ->where([
                'status' => true,
                'group' => config('terminal.api.sliders.app_guide'),
            ])
            ->orderBy('sort', 'desc')
            ->get();
        return api_result(0, null, new SliderResource($items));
    }
}
