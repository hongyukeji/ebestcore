<?php

namespace System\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ArticleToTag extends Pivot
{
    /**
     * 与模型关联的数据表
     *
     * @var string
     */
    protected $table = 'article_to_tags';

    /**
     * 该模型是否被自动维护时间戳
     *
     * @var bool
     */
    public $timestamps = false;
}
