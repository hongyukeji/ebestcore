<?php

namespace System\Http\Controllers\Api\V1\Page;

use System\Http\Controllers\Api\Controller;
use System\Models\Link;
use System\Models\Navigation;

class UserController extends Controller
{
    public function index()
    {
        $items['more_navigations'] = Navigation::query()
            ->where([
                'status' => true,
                'group' => config('terminal.api.navigations.user_more_navigations'),
            ])
            ->orderBy('sort', 'desc')
            ->get();
        $items['link_vip'] = Link::query()
            ->where([
                'status' => true,
                'group' => config('terminal.api.links.user_link_vip'),
            ])
            ->orderBy('sort', 'desc')
            ->get();
        return api_result(0, null, $items);
    }
}
