<?php

namespace System\Presenters;

use System\Models\Advert;
use System\Models\Article;
use System\Models\ArticleCategory;
use System\Models\Link;
use System\Models\Navigation;
use System\Models\Product;
use System\Models\Slider;

class FrontendPresenter
{
    /*
     * 获取首页轮播图
     */
    public function getHomeSliders()
    {
        return $this->getSliders(Slider::FRONTEND_HOME_SLIDER);
    }

    /*
     * 获取首页文章
     */
    public function getHomeArticleGroup($group_limit = 0)
    {
        $article_group = [];
        $article_categories = config('terminal.web.articles.home_article_categories', []);
        $sorted = array_values(array_sort($article_categories, function ($value) {
            return $value['sort'];
        }));
        foreach ($sorted as $category) {
            $article_category = ArticleCategory::query()->find($category['id']);
            if ($article_category) {
                $articles = Article::query()->where('article_category_id', $category['id'])->limit($group_limit)->get();
                $article_group[$article_category->name] = $articles;
            }
        }
        return $article_group;
    }

    /*
     * 首页顶部活动图片
     */
    public function getHeaderTopActivity()
    {
        return Advert::query()
            ->where('group', 'frontend_header_top_activity')
            ->where('status', true)
            ->first();
    }

    /*
     * 首页-特色-广告
     */
    public function getHomeFeatures($number = null)
    {
        return Advert::query()
            ->where('group', 'frontend_home_features')
            ->where('status', true)
            ->orderBy('sort', 'desc')
            ->limit($number)
            ->get();
    }

    public function getHomeMainNavigationRight()
    {
        return Advert::query()
            ->where('group', 'frontend_home_main_navigation_right')
            ->where('status', true)
            ->first();
    }

    /*
     * 电脑端-首页-轮播图-右侧广告
     */
    public function getHomeSliderRight()
    {
        return Advert::query()
            ->where('group', 'frontend_home_slider_right')
            ->where('status', true)
            ->first();
    }

    /*
     * 电脑端-首页-秒杀-右侧广告
     */
    public function getHomeSecKillRight()
    {
        return Advert::query()
            ->where('group', 'frontend_home_sec_kill_right')
            ->where('status', true)
            ->first();
    }

    /*
     * 电脑端-首页-购买活动入口-广告
     */
    public function getHomeBuyActivityEntry($number = null)
    {
        return Advert::query()
            ->where('group', 'frontend_home_buy_activity_entry')
            ->where('status', true)
            ->orderBy('sort', 'desc')
            ->limit($number)
            ->get();
    }

    /*
     * 电脑端-首页-横幅-01-广告
     */
    public function getHomeBannerThreeOneLevel($number = null)
    {
        return Advert::query()
            ->where('group', 'frontend_home_banner_three_one_level')
            ->where('status', true)
            ->orderBy('sort', 'desc')
            ->limit($number)
            ->get();
    }

    /*
     * 电脑端-首页-横幅-02-广告
     */
    public function getHomeBannerThreeTwoLevel()
    {
        return Advert::query()
            ->where('group', 'frontend_home_banner_three_two_level')
            ->where('status', true)
            ->orderBy('sort', 'desc')
            ->get();
    }

    /*
     * 登录页背景图广告
     */
    public function getLoginBackgroundImage()
    {
        return Advert::query()
            ->where('group', 'frontend_login_activity_image')
            ->where('status', true)
            ->first();
    }

    /*
     * 注册页面广告
     */
    public function getRegisterActivityImage()
    {
        return Advert::query()
            ->where('group', 'frontend_register_activity_image')
            ->where('status', true)
            ->first();
    }

    /*
     * 用户反馈
     */
    public function getUserFeedback()
    {
        return Link::query()
            ->where('group', 'frontend_user_feedback')
            ->where('status', true)
            ->first();
    }

    /*
     * 登录页面-调查问卷
     */
    public function getLoginQuestionnaire()
    {
        return Link::query()
            ->where('group', 'frontend_login_questionnaire')
            ->where('status', true)
            ->first();
    }

    /*
     * 公共页-头部-关键字
     */
    public function getLayoutHeaderKeyword()
    {
        return Link::query()
            ->where('group', Link::FRONTEND_LAYOUT_HEADER_KEYWORD)
            ->where('status', true)
            ->orderBy('sort', 'desc')
            ->get();
    }

    /*
     * 关注我们
     */
    public function getHeaderFollowUsLinks()
    {
        return $this->getLinks(Link::FRONTEND_HEADER_FOLLOW_US);
    }

    /*
     * 客户服务
     */
    public function getHeaderCustomerServiceLinks()
    {
        return $this->getLinks(Link::FRONTEND_HEADER_CUSTOMER_SERVICE);
    }

    public function getHeaderWebsiteNavigations()
    {
        return Link::query()
            ->where('group', Link::FRONTEND_HEADER_WEBSITE_NAVIGATION)
            ->where('status', true)
            ->orderBy('sort', 'desc')
            ->get()
            ->groupBy('description');
    }

    /*
     * 首页底部服务
     */
    public function getHomeFooterServiceLinks($number = null)
    {
        return $this->getLinks(Link::FRONTEND_HOME_FOOTER_SERVICE, $number);
    }

    /*
     * 底部帮助
     */
    public function getFooterHelpLinks()
    {
        return Link::query()
            ->where('group', Link::FRONTEND_FOOTER_HELP_LINK)
            ->where('status', true)
            ->orderBy('sort', 'desc')
            ->get()
            ->groupBy('description');
    }

    /*
     * 公共页面-底部链接
     */
    public function getFooterNavigations()
    {
        return $this->getNavigations(Link::FRONTEND_FOOTER_NAVIGATION);
    }

    /*
     * 商品详情页-举报-链接
     */
    public function getProductShowReportLink()
    {
        return Link::query()
            ->where('group', 'frontend_product_show_report')
            ->where('status', true)
            ->first();
    }

    /*
     * 公共页面-主导航
     */
    public function getMainNavigations()
    {
        return $this->getNavigations(Navigation::FRONTEND_MAIN_NAVIGATION);
    }

    /*
     * 链接查询
     */
    public function getLinks($group, $number = null)
    {
        return Link::query()
            ->where('group', $group)
            ->where('status', true)
            ->orderBy('sort', 'desc')
            ->limit($number)
            ->get();
    }

    /*
     * 导航查询
     */
    public function getNavigations($group)
    {
        return Navigation::query()
            ->where('group', $group)
            ->where('status', true)
            ->orderBy('sort', 'desc')
            ->get();
    }

    /*
     * 轮播图查询
     */
    public function getSliders($group)
    {
        return Slider::query()
            ->where('group', $group)
            ->where('status', true)
            ->orderBy('sort', 'desc')
            ->get();
    }

    public function getAdverts($group)
    {
        return Advert::query()
            ->where('group', $group)
            ->where('status', true)
            ->orderBy('sort', 'desc')
            ->get();
    }

    /*
     * 秒杀商品列表
     */
    public function getSecKillProduct($number = 10)
    {
        return Product::query()
            ->where('status', true)
            ->orderBy('created_at', 'desc')
            ->take($number)
            ->get();
    }

    public function getRandomProduct($number = 10, $except_ids = [])
    {
        return Product::query()
            ->whereNotIn('id', $except_ids)
            ->where('status', true)
            ->inRandomOrder()
            ->take($number)
            ->get();
    }
}
