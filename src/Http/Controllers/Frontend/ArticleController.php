<?php

namespace System\Http\Controllers\Frontend;

use System\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index()
    {
    }

    public function show(Article $article)
    {
        $article || !$article->status or abort(404);
        event(new \System\Events\Articles\BrowseArticleEvent($article));
        return view('frontend::articles.show', compact('article'));
    }
}
