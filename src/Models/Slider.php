<?php

namespace System\Models;

use QCod\ImageUp\HasImageUploads;

class Slider extends Model
{
    use HasImageUploads;

    const FRONTEND_HOME_SLIDER = 'frontend_home_slider';
    const MOBILE_HOME_SLIDER = 'mobile_home_slider';
    const API_HOME_SLIDER = 'api_home_slider';
    const APP_GUIDE_SLIDER = 'app_guide_slider';

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
                'path' => uploads_path('slider'),
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
