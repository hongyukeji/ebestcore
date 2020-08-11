<?php

namespace System\Models;

use QCod\ImageUp\HasImageUploads;

class Article extends Model
{
    use HasImageUploads;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setImagesField([
            'image' => [
                'path' => uploads_path('article.image'),
                'disk' => config('filesystems.default', 'public'),
            ],
        ]);
    }

    public function category()
    {
        return $this->belongsTo(ArticleCategory::class, 'article_category_id', 'id')->withDefault([
            'id' => 0,
            'name' => trans('common.default') . trans('common.category'),
        ]);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->withDefault([
            'id' => '0',
            'name' => trans('common.nameless'),
            'avatar' => config('params.users.default_avatar'),
        ]);
    }

    public function tags()
    {
        return $this->belongsToMany(ArticleTag::class, 'article_to_tags')->using(ArticleToTag::class);
    }

    public function comments()
    {
        return $this->hasMany(ArticleComment::class, 'article_id', 'id')->orderBy('created_at', 'desc');
    }

    /**
     * 防止XSS攻击
     *
     * @param $value
     */
    public function setContentAttribute($value)
    {
        $this->attributes['content'] = clean($value, 'text_content');
    }
}
