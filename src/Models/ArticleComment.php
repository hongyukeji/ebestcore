<?php

namespace System\Models;

use System\Traits\Models\CategoryTrait;

class ArticleComment extends Model
{
    use CategoryTrait;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault([
            'name' => trans('common.nameless'),
            'avatar' => config('params.users.default_avatar'),
        ]);
    }
}
