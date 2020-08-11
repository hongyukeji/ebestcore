<?php

namespace System\Observers;

use System\Models\Article;
use Illuminate\Support\Facades\Log;

class ArticleObserver
{

    /**
     * 监听数据即将创建的事件。
     *
     * @param Article $article
     * @return void
     */
    public function creating(Article $article)
    {

    }

    /**
     * 监听数据创建后的事件。
     *
     * @param Article $article
     * @return void
     */
    public function created(Article $article)
    {

    }

    /**
     * 监听数据即将更新的事件。
     *
     * @param Article $article
     * @return void
     */
    public function updating(Article $article)
    {

    }

    /**
     * 监听数据更新后的事件。
     *
     * @param Article $article
     * @return void
     */
    public function updated(Article $article)
    {

    }

    /**
     * 监听数据即将保存的事件。
     *
     * @param Article $article
     * @return void
     */
    public function saving(Article $article)
    {

    }

    /**
     * 监听数据保存后的事件。
     *
     * @param Article $article
     * @return void
     */
    public function saved(Article $article)
    {

    }

    /**
     * 监听数据即将删除的事件。
     *
     * @param Article $article
     * @return void
     */
    public function deleting(Article $article)
    {
        // 删除详情图片
        try {
            $file_paths = collect();
            $preg = '/<img.*?src=[\"|\']?(.*?)[\"|\']?\s.*?>/i';
            if ($article->content) {
                preg_match_all($preg, $article->content, $allImg);
                if (isset($allImg[1])) {
                    $file_paths = $file_paths->merge($allImg[1]);
                }
            }
            $prefix = config('app.url');
            $file_paths->each(function ($item, $key) use ($prefix) {
                $file = str_after($item, $prefix);
                if (\Illuminate\Support\Facades\Storage::exists($file)) {
                    \Illuminate\Support\Facades\Storage::delete($file);
                }
            });
        } catch (\Exception $e) {
            Log::warning($e->getMessage());
        }
    }

    /**
     * 监听数据删除后的事件。
     *
     * @param Article $article
     * @return void
     */
    public function deleted(Article $article)
    {

    }

    /**
     * 监听数据即将从软删除状态恢复的事件。
     *
     * @param Article $article
     * @return void
     */
    public function restoring(Article $article)
    {

    }

    /**
     * 监听数据从软删除状态恢复后的事件。
     *
     * @param Article $article
     * @return void
     */
    public function restored(Article $article)
    {

    }
}
