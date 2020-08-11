<?php

namespace System\Http\Controllers\Api\V1\Page;

use System\Http\Controllers\Api\Controller;
use System\Http\Resources\SiteResource;

class SiteController extends Controller
{
    public function index()
    {
        return api_result(0, null, new SiteResource(config('websites.basic')));
    }
}
