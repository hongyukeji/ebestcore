<?php

namespace System\Models;

use System\Traits\Models\CategoryTrait;
use QCod\ImageUp\HasImageUploads;

class Navigation extends Model
{
    use CategoryTrait, HasImageUploads;

    const FRONTEND_MAIN_NAVIGATION = 'frontend_main_navigation';
    const MOBILE_HOME_NAVIGATION = 'mobile_home_navigation';
    const MOBILE_USER_NAVIGATION = 'mobile_user_navigation';
    const MOBILE_USER_MORE_NAVIGATIONS = 'mobile_user_more_navigations';
    const Api_HOME_NAVIGATION = 'api_home_navigation';

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
                'path' => uploads_path('navigation'),
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
