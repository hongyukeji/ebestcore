<?php

namespace System\Services;

use Illuminate\Pagination\LengthAwarePaginator;

class ArrayService extends Service
{
    public $array;

    public function __construct($array = [])
    {
        $this->array = $array;
    }

    public function paginate($perPage = 15, $page = null, $options = [])
    {
        $data = $this->array;
        //当前页数 默认1
        $page = request()->page ?: 1;
        //每页的条数
        //$perPage = $perPage;
        //计算每页分页的初始位置
        $offset = ($page * $perPage) - $perPage;
        //实例化LengthAwarePaginator类，并传入对应的参数
        return new LengthAwarePaginator(array_slice($data, $offset, $perPage, true), count($data), $perPage, $page, ['path' => request()->url(), 'query' => request()->query()]);
    }
}
