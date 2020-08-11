<?php

namespace System\Models;

use System\Traits\Models\CategoryTrait;
use QCod\ImageUp\HasImageUploads;

class ArticleCategory extends Model
{
    use CategoryTrait, HasImageUploads;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setImagesField([
            'image' => [
                'path' => uploads_path('article_category'),
                'disk' => config('filesystems.default', 'public'),
            ],
        ]);
    }
}
