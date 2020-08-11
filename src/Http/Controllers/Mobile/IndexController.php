<?php

namespace System\Http\Controllers\Mobile;

use System\Models\Advert;
use System\Models\Article;
use System\Models\Navigation;
use System\Models\Slider;
use System\Services\ProductService;

class IndexController extends Controller
{
    public function index()
    {
        $adverts['home_top_group'] = Advert::query()
            ->where([
                'status' => true,
                'group' => config('terminal.mobile.adverts.home_top_group'),
            ])
            ->orderBy('sort', 'desc')
            ->first();
        $sliders = Slider::query()
            ->where([
                'status' => true,
                'group' => config('terminal.mobile.sliders.home_group'),
            ])
            ->orderBy('sort', 'desc')
            ->get();
        $navigations = Navigation::query()
            ->where([
                'status' => true,
                'group' => config('terminal.mobile.navigations.home_group'),
            ])
            ->orderBy('sort', 'desc')
            ->get();
        $articles = Article::query()
            ->where([
                'status' => true,
                'article_category_id' => config('terminal.mobile.articles.home_article_category_id'),
            ])
            ->orderBy('sort', 'desc')
            ->get();
        $adverts['slider_spike'] = Advert::query()
            ->where([
                'status' => true,
                'group' => config('terminal.mobile.adverts.home_slider_spike'),
            ])
            ->orderBy('sort', 'desc')
            ->get();
        $adverts['slider_storey'] = Advert::query()
            ->where([
                'status' => true,
                'group' => config('terminal.mobile.adverts.home_slider_storey'),
            ])
            ->orderBy('sort', 'desc')
            ->get();
        $adverts['slider_recommend'] = Advert::query()
            ->where([
                'status' => true,
                'group' => config('terminal.mobile.adverts.home_slider_recommend'),
            ])
            ->orderBy('sort', 'desc')
            ->get();
        $adverts['activity_entry'] = Advert::query()
            ->where([
                'status' => true,
                'group' => config('terminal.mobile.adverts.home_activity_entry'),
            ])
            ->orderBy('sort', 'desc')
            ->get();
        $adverts['activity_group'] = Advert::query()
            ->where([
                'status' => true,
                'group' => config('terminal.mobile.adverts.home_activity_group'),
            ])
            ->orderBy('sort', 'desc')
            ->get();
        $adverts['storey_group_01'] = Advert::query()
            ->where([
                'status' => true,
                'group' => config('terminal.mobile.adverts.home_storey_group_01'),
            ])
            ->orderBy('sort', 'desc')
            ->get();
        $adverts['storey_group_02'] = Advert::query()
            ->where([
                'status' => true,
                'group' => config('terminal.mobile.adverts.home_storey_group_02'),
            ])
            ->orderBy('sort', 'desc')
            ->get();
        return view('mobile::index.index', compact('adverts', 'sliders', 'articles', 'navigations'));
    }
}
