<?php

namespace System\Http\Controllers\Mobile;

use System\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
    }

    public function show(Article $article)
    {
        $article or abort(404);
        event(new \System\Events\Articles\BrowseArticleEvent($article));
        return view('mobile::articles.show', compact('article'));
    }
}
