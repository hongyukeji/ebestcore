<?php

namespace System\Listeners\Articles;

use System\Models\Article;
use Illuminate\Support\Facades\Cache;
use System\Events\Articles\BrowseArticleEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class BrowseArticleListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param BrowseArticleEvent $event
     * @return void
     */
    public function handle(BrowseArticleEvent $event)
    {
        $article = $event->article;
        $session = request()->session()->getId() ?: uuid();
        $cache_prefix = 'browse_article_';
        $cache_key = $cache_prefix . $session . $article->id;

        // 缓存不存在则文章浏览次数+1，并缓存该文章键值，缓存有效期为12小时
        if (!Cache::has($cache_key)) {
            $article->update(['browse_count' => (int)$article->browse_count + 1]);
            Cache::put($cache_key, $article->id, now()->addHours(12));
        }
    }
}
