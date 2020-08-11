<?php

namespace System\Traits\Repository;

use Illuminate\Support\Carbon;

trait CountRepositoryTrait
{
    /*
     * 获取新增数量
     */
    public function createCount($shop_id = null)
    {
        $data = [];

        // 时间 whereTime / 日期 whereDate

        // 今天数据
        $data['today_count'] = self::createCountModel()->whereDate('created_at', Carbon::today())->count();
        // 昨天数据
        $data['yesterday_count'] = self::createCountModel()->whereDate('created_at', Carbon::yesterday())->count();
        // 本周数据
        $this_week = [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()];
        $data['this_week_count'] = self::createCountModel()->whereBetween('created_at', $this_week)->count();
        // 上周数据
        $last_week = [Carbon::now()->startOfWeek()->subWeek(), Carbon::now()->endOfWeek()->subWeek()];
        $data['last_week_count'] = self::createCountModel()->whereBetween('created_at', $last_week)->count();
        // 本月数据
        $data['this_month_count'] = self::createCountModel()->whereMonth('created_at', Carbon::now()->month)->count();
        // 上月数据
        $data['last_month_count'] = self::createCountModel()->whereMonth('created_at', Carbon::now()->subMonth()->month)->count();
        // 本年数据
        $data['this_year_count'] = self::createCountModel()->whereYear('created_at', Carbon::now()->year)->count();
        // 上一年
        $data['last_year_count'] = self::createCountModel()->whereYear('created_at', Carbon::now()->subYear()->year)->count();

        return $data;
    }
}
