<?php

namespace System\Models;

use QCod\ImageUp\HasImageUploads;

class Advert extends Model
{
    use HasImageUploads;

    /**
     * 追加到模型数组表单的访问器。
     *
     * @var array
     */
    protected $appends = [
        'image_url',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setImagesField([
            'image' => [
                'path' => uploads_path('advert'),
                'disk' => config('filesystems.default', 'public'),
            ],
        ]);
    }

    public function getImageUrlAttribute()
    {
        if (!empty($this->image)) {
            return asset_url($this->image);
        } else {
            return asset_url(config('params.products.default_image'));
        }
    }
}
