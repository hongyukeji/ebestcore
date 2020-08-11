<?php

namespace System\Models;

use QCod\ImageUp\HasImageUploads;

class Link extends Model
{
    use HasImageUploads;

    const FRONTEND_HEADER_FOLLOW_US = 'frontend_header_follow_us';
    const FRONTEND_HEADER_CUSTOMER_SERVICE = 'frontend_header_customer_service';
    const FRONTEND_HOME_FOOTER_SERVICE = 'frontend_home_footer_service';
    const FRONTEND_FOOTER_NAVIGATION = 'frontend_footer_navigation';
    const FRONTEND_FOOTER_HELP_LINK = 'frontend_footer_help_link';
    const FRONTEND_HEADER_WEBSITE_NAVIGATION = 'frontend_header_website_navigation';
    const FRONTEND_LAYOUT_HEADER_KEYWORD = 'frontend_layout_header_keyword';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setImagesField([
            'image' => [
                'path' => uploads_path('link'),
                'disk' => config('filesystems.default', 'public'),
            ],
        ]);
    }
}
