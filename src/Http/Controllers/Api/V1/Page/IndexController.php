<?php

namespace System\Http\Controllers\Api\V1\Page;

use System\Http\Controllers\Api\Controller;
use System\Models\Advert;
use System\Models\Article;
use System\Models\Navigation;
use System\Models\Slider;
use System\Http\Resources\SiteResource;
use System\Services\ProductService;

class IndexController extends Controller
{
    public function index()
    {
        $items['websites'] = new SiteResource(config('websites.basic'));
        $items['sliders'] = Slider::query()
            ->where([
                'status' => true,
                'group' => config('terminal.api.sliders.home_group'),
            ])
            ->orderBy('sort', 'desc')
            ->get();
        $items['navigations'] = Navigation::query()
            ->where([
                'status' => true,
                'group' => config('terminal.api.navigations.home_group'),
            ])
            ->orderBy('sort', 'desc')
            ->get();
        $items['articles'] = Article::query()
            ->where([
                'status' => true,
                'article_category_id' => config('terminal.api.articles.home_article_category_id'),
            ])
            ->orderBy('sort', 'desc')
            ->get();
        $items['products'] = [
            [
                'title' => '精选',
                'description' => '为你推荐',
                'list' => (new ProductService())->getBests(10),
            ], [
                'title' => '热卖',
                'description' => '热销产品',
                'list' => (new ProductService())->getHots(10),
            ], [
                'title' => '新品',
                'description' => '全新上架',
                'list' => (new ProductService())->getNews(10),
            ],
        ];
        $items['advert_group_01'] = Advert::query()
            ->where([
                'status' => true,
                'group' => config('terminal.api.adverts.home_advert_group_01'),
            ])
            ->orderBy('sort', 'desc')
            ->get();
        $items['advert_group_02'] = Advert::query()
            ->where([
                'status' => true,
                'group' => config('terminal.api.adverts.home_advert_group_02'),
            ])
            ->orderBy('sort', 'desc')
            ->get();
        $items['advert_group_03'] = Advert::query()
            ->where([
                'status' => true,
                'group' => config('terminal.api.adverts.home_advert_group_03'),
            ])
            ->orderBy('sort', 'desc')
            ->get();
        return api_result(0, null, $items);
    }
}
