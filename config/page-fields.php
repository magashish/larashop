<?php

return [
    // Home page configuration (slug: 'home')
    'home' => [
        'text_fields' => [
            'banner_text',
            'hero_title_line1',
            'hero_title_line2',
            'hero_rating_value',
            'hero_subscribers_text',
            'hero_roadmap_link',
            'cash_giveaway_title_line1',
            'cash_giveaway_title_line2',
            'cash_giveaway_description',
            'cash_giveaway_date',
            'work_tab_title_line1',
            'work_tab_title_line2',
            'work_tab_paragraph',
            'rewards_tab_title_line1',
            'rewards_tab_title_line2',
            'rewards_tab_paragraph',
            'community_tab_title_line1',
            'community_tab_title_line2',
            'community_tab_paragraph',
            'lifestyle_tab_title_line1',
            'lifestyle_tab_title_line2',
            'lifestyle_tab_paragraph',
        ],
        'image_fields' => [
            'hero_img_1',
            'mobile_hero_img',
            'hero_rating_users_img',
            'hero_rating_icon_img',
            'cash_giveaway_image',
            'work_tab_image',
            'rewards_tab_image',
            'community_tab_image',
            'lifestyle_tab_image',
            'hero_video_poster',
        ],
        'no_webp_fields' => [
            'hero_img_2'
        ],
        'video_fields' => [
            'hero_video_src',
        ],
        'slider_fields' => [
            'hero_slider',
            'hero_slider_mobile'
        ],
        'dynamic_slider_fields' => [
            'hero_dynamic_slider'
        ],
    ],
    
    // Landing page configuration (slug: 'landing-page')
    'landing-page' => [
        'text_fields' => [
            'landing_package_title',
            'landing_package_subtitle',
            'landing_worth_title',
            'landing_worth_subtitle',
            'landing_real_title',
            'landing_real_subtitle',
            'landing_past_winner_title',
            'landing_past_winner_subtitle',
            'landing_harcourt_title',
            'landing_harcourt_subtitle',
            'landing_harcourt_description',
        ],
        'image_fields' => [
            'landing_harcourt_image_1',
            'landing_harcourt_image_2',
            'landing_real_video_poster',
        ],
        'no_webp_fields' => [
            'landing_hero_image',
            'landing_package_image',
            'landing_past_winner_image',
        ],
        'video_fields' => [
            'landing_real_video_src'
        ],
        'slider_fields' => [
            'landing_banner_slider',
            'landing_banner_mobile_slider',
            'landing_worth_slider',
            'landing_past_winner_slider'
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Mazda page configuration (slug: mazda-page)
    |--------------------------------------------------------------------------
    */
    'mazda-page' => [
        'text_fields' => [
            'mazda_title',
            'mazda_subtitle',
            'mazda_exclusive_title',
            'mazda_package_title',
            'mazda_package_subtitle',
            'mazda_past_winner_title',
            'mazda_past_winner_subtitle',
            'mazda_harcourt_title',
            'mazda_harcourt_subtitle',
            'mazda_harcourt_description',
        ],
        'image_fields' => [
            'mazda_harcourt_image_1',
            'mazda_harcourt_image_2',
        ],
        'no_webp_fields' => [
            'mazda_hero_image',
            'mazda_sub_hero_image',
            'mazda_past_winner_image',
        ],
        'video_fields' => [
        ],
        'slider_fields' => [
            'mazda_past_winner_slider',
        ],
        'select2_offers_fields' => [
            'mazda_featured_offers',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Grange page configuration (slug: grange-page)
    |--------------------------------------------------------------------------
    */
    'grange-page' => [
        'text_fields' => [
            'grange_title',
            'grange_subtitle',
            'grange_exclusive_title',
            'grange_package_title',
            'grange_package_subtitle',
            'grange_past_winner_title',
            'grange_past_winner_subtitle',
            'grange_harcourt_title',
            'grange_harcourt_subtitle',
            'grange_harcourt_description',
        ],
        'image_fields' => [
            'grange_harcourt_image_1',
            'grange_harcourt_image_2',
        ],
        'no_webp_fields' => [
            'grange_hero_image',
            'grange_sub_hero_image',
            'grange_past_winner_image',
        ],
        'video_fields' => [
        ],
        'slider_fields' => [
            'grange_past_winner_slider',
        ],
        'select2_offers_fields' => [
            'grange_featured_offers',
        ],
    ],
];