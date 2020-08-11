<?php

return [
    'api' => [
        'bases' => [
            'debug' => false,
        ],
        'sliders' => [
            'home_group' => 'mobile_home_slider',
            'app_guide' => 'app_guide_slider',
        ],
        'navigations' => [
            'home_group' => 'mobile_home_navigation',
            'user_more_navigations' => 'mobile_user_more_navigations',
        ],
        'adverts' => [
            'home_top_group' => 'mobile_home_top_advert',
            'home_advert_group_01' => 'api_home_advert_group_01',
            'home_advert_group_02' => 'api_home_advert_group_02',
            'home_advert_group_03' => 'api_home_advert_group_03',
        ],
        'articles' => [
            'home_article_category_id' => '1',
        ],
        'links' => [
            'user_link_vip' => 'api_user_link_vip',
        ]
    ],
    'mobile' => [
        'bases' => [
            'debug' => false,
            'home_page_article' => true,
        ],
        'sliders' => [
            'home_group' => 'mobile_home_slider',
        ],
        'navigations' => [
            'home_group' => 'mobile_home_navigation',
            'user_more_navigations' => 'mobile_user_more_navigations',
        ],
        'adverts' => [
            'home_top_group' => 'mobile_home_top_advert',
            'home_slider_spike' => 'mobile_home_advert_slider_spike',
            'home_slider_storey' => 'mobile_home_advert_slider_storey',
            'home_slider_recommend' => 'mobile_home_advert_slider_recommend',
            'home_activity_entry' => 'mobile_home_advert_activity_entry',
            'home_activity_group' => 'mobile_home_advert_activity_group',
            'home_storey_group_01' => 'mobile_home_advert_storey_group_01',
            'home_storey_group_02' => 'mobile_home_advert_storey_group_02',
        ],
        'articles' => [
            'home_article_category_id' => '1',
        ],
    ],
    'web' => [
        'adverts' => [
            'home_features' => 'frontend_home_features',
        ],
        'articles' => [
            'home_article_categories' => [
                'notice' => [
                    'id' => '1',
                    'sort' => '100',
                ],
                'activity' => [
                    'id' => '2',
                    'sort' => '200',
                ],
                'help' => [
                    'id' => '3',
                    'sort' => '300',
                ],
            ],
        ],
    ],
];
