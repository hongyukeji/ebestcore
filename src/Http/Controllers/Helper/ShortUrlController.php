<?php

namespace System\Http\Controllers\Helper;

use System\Models\ShortUrl;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ShortUrlController extends Controller
{
    public function index($code)
    {
        $cache_prefix = config('params.cache.commons.short_url.prefix', 'common_short_url_');
        $short_url_cache_prefix = Str::slug($cache_prefix . $code, '_');

        $url = Cache::rememberForever($short_url_cache_prefix, function () use ($code) {
            $short_url = ShortUrl::query()->where('code', $code)->first() or abort(404);
            return $short_url->url;
        });

        return redirect($url);
    }
}
