<?php

namespace System\Services;

use System\Models\Product;

class ProductService extends Service
{
    public function getBests($limit_number = null)
    {
        return \System\Models\Product::query()
            ->where('status', Product::STATUS_ACTIVE)
            ->where('is_best', true)
            ->orderBy('sort', 'desc')
            ->take($limit_number)
            ->get();
    }

    public function getHots($limit_number = null)
    {
        return \System\Models\Product::query()
            ->where('status', Product::STATUS_ACTIVE)
            ->where('is_hot', true)
            ->orderBy('sort', 'desc')
            ->take($limit_number)
            ->get();
    }

    public function getNews($limit_number = null)
    {
        return \System\Models\Product::query()
            ->where('status', Product::STATUS_ACTIVE)
            ->where('is_new', true)
            ->orderBy('sort', 'desc')
            ->take($limit_number)
            ->get();
    }

    public function formatImageUrl($content = "")
    {
        /*$prefix = get_storage_disk('url');*/
        /*$pregRule = "/<[img|IMG].*?src=[\'|\"](.*?(?:[\.jpg|\.jpeg|\.png|\.gif|\.bmp]))[\'|\"].*?[\/]?>/";*/
        /*$content = preg_replace($pregRule, '<img src="' . $prefix . '${1}" style="max-width:100%"/>', $content);*/
        $pattern = "/<[img|IMG].*?src=[\'|\"](.*?(?:[\.gif|\.jpg|\.png|\.jpeg]))[\'|\"].*?[\/]?>/i";
        preg_replace_callback($pattern, function ($ma) {
            return str_replace($ma[1], asset_url($ma[1]), $ma[0]);
        }, $content);
        return $content;
    }

}
