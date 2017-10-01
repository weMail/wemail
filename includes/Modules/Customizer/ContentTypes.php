<?php

namespace WeDevs\WeMail\Modules\Customizer;

class ContentTypes {

    public static $image_dir;

    public static function get_type_settings( $context = '' ) {
        self::$image_dir = WEMAIL_ASSETS . '/images/content-types';

        $settings = [
            'text'          => self::text(),
            'image'         => self::image(),
            'imageGroup'    => self::image_group(),
            'imageCaption'  => self::image_caption(),
            'socialFollow'  => self::social_follow(),
            'button'        => self::button(),
            'divider'       => self::divider(),
            'video'         => self::video(),
            'footer'        => self::footer(),
        ];

        /**
         * Filter for weMail customizer content type settings
         *
         * $context  Which customizer we are working on, campaign, woocommerce, wp or else.
         *
         * @since 1.0.0
         *
         * @param array $settings Content type names and their data.
         */
        return apply_filters( "wemail_customizer_content_type_settings_{$context}", $settings );
    }

    public static function text() {
        return [
            'type'       => 'text',
            'image'      => self::$image_dir . '/text.png',
            'default'    => [
                'style'      => [
                    'backgroundColor' => '#ffffff',
                    'color'           => '',
                    'paddingTop'      => '15px',
                    'paddingBottom'   => '15px',
                    'paddingLeft'     => '15px',
                    'paddingRight'    => '15px',
                    'borderWidth'     => '0px',
                    'borderStyle'     => 'solid',
                    'borderColor'     => '#e5e5e5'
                ],
                'twoColumns' => true,
                'texts'      => [
                    sprintf( '<p>%s</p>', __( 'This is a text block. You can use it to add text to your template.', 'wemail' ) ),
                    sprintf( '<p>%s</p>', __( 'This is a text block 2. You can use it to add text to your template.', 'wemail' ) )
                ],

                'valign'     => 'top',
            ],
            'noSettingsTab' => false
        ];
    }

    public static function image() {
        return [
            'type'       => 'image',
            'image'   => self::$image_dir . '/image.png',
            'default' => [
                'style'  => [
                    'backgroundColor' => '#ffffff',
                    'padding'         => '15px',
                    'marginLeft'      => '0px',
                    'marginRight'     => '0px',
                    'borderWidth'     => '0px',
                    'borderStyle'     => 'solid',
                    'borderColor'     => '#e5e5e5',
                    'textAlign'       => 'center',
                ],
                'images' => [],
                'widths' => [ '0px' ]
            ]
        ];
    }

    public static function image_group() {
        return [
            'type'        => 'imageGroup',
            'image'   => self::$image_dir . '/image-group.png',
            'default' => [
                'style'  => [
                    'backgroundColor' => '#ffffff',
                    'padding'         => '15px',
                    'marginLeft'      => '0px',
                    'marginRight'     => '0px',
                    'borderWidth'     => '0px',
                    'borderStyle'     => 'solid',
                    'borderColor'     => '#e5e5e5',
                    'textAlign'       => 'center',
                ],
                'images' => [],
                'widths' => [ '0px', '0px', '0px' ],
                'layout' => 'r1-r1',
            ]
        ];
    }

    public static function image_caption() {
        return [
            'type'        => 'imageCaption',
            'image'       => self::$image_dir . '/image-caption.png',
            'defaultText' => sprintf( '<p>%s</p>', __( 'Your text caption goes here. You can change the position of the caption and set styles in the block’s settings tab.', 'wemail' ) ),
            'default'     => [
                'style'       => [
                    'backgroundColor' => '#ffffff',
                    'padding'         => '15px 15px',
                    'fontSize'        => '14px',
                    'borderWidth'     => '0px',
                    'borderStyle'     => 'solid',
                    'borderColor' => '#e5e5e5'
                ],
                'twoColumns'  => false,
                'groups'      => [],
                'capPosition' => 'bottom',
            ]
        ];
    }

    public static function social_follow() {
        return [
            'type'        => 'socialFollow',
            'image'      => self::$image_dir . '/social-follow.png',
            'default'    => [
                'style'     => [
                    'backgroundColor' => '#ffffff',
                    'padding'         => '15px',
                    'borderWidth'     => '0px',
                    'borderStyle'     => 'none',
                    'borderColor'     => '',
                    'fontSize'        => '14px',
                    'fontWeight'      => 'normal',
                    'textTransform'   => 'none',
                    'lineHeight'      => '0px'
                ],
                'iconStyle' => 'solid-color',
                'icons'     => [
                    [
                        'site' => 'facebook',
                        'link' => 'http://facebook.com',
                        'text' => 'Facebook',
                    ],
                    [
                        'site' => 'twitter',
                        'link' => 'http://twitter.com',
                        'text' => 'Twitter',
                    ],
                    [
                        'site' => 'link',
                        'link' => 'http://example.com',
                        'text' => __( 'Website', 'wemail' ),
                    ]
                ],
                'iconMargin'     => '15px',
                'display'        => 'both', // icon/text/both
                'containerAlign' => 'center',
                'layout'         => 'horizontal', // verticle/horizontal
                'layoutSize'     => 'default', // default/big
            ]
        ];
    }

    public static function button() {
        return [
            'type'        => 'button',
            'image'     => self::$image_dir . '/button.png',
            'default'   => [
                'style' => [
                    'display'         => 'inline-block',
                    'padding'         => '18px 65px',
                    'margin'          => '15px 15px',
                    'fontFamily'      => 'sans-serif',
                    'fontSize'        => '14px',
                    'fontWeight'      => 'bold',
                    'lineHeight'      => '1',
                    'color'           => '#fff',
                    'textAlign'       => 'center',
                    'textDecoration'  => 'none',
                    'textTransform'   => 'none',
                    'backgroundColor' => '#2980b9',
                    'border'          => '0px none #176598',
                    'borderRadius'    => '3px',
                    'borderWidth'     => '0px',
                    'borderStyle'     => 'solid',
                    'borderColor'     => '#e5e5e5'
                ],
                'text'           => __( 'Button Text', 'wemail' ),
                'link'           => '#',
                'title'          => '',
                'containerStyle' => [
                    'textAlign'       => 'center',
                    'backgroundColor' => '#ffffff'
                ]
            ]
        ];
    }

    public static function divider() {
        return [
            'type'        => 'divider',
            'image'           => self::$image_dir . '/divider.png',
            'noStyleSettings' => true,
            'default'         => [
                'containerStyle' => [
                    'paddingTop'      => '15px',
                    'paddingBottom'   => '15px',
                    'backgroundColor' => '#ffffff',
                    'marginTop'       => '0px',
                    'marginBottom'    => '0px'
                ],
                'style' => [
                    'width'           => '600px',
                    'borderTopWidth'  => '2px',
                    'borderTopStyle'  => 'dashed',
                    'borderTopColor'  => '#e5e5e5',
                ],
                'useImage'  => false,
                'image'     => [
                    'image' => WEMAIL_ASSETS . '/images/dividers/brush-stroke-lite.png',
                    'style' => [
                        'height' => '7px',
                        'width'  => '600px',
                    ]
                ]
            ]
        ];
    }

    public static function video() {
        return [
            'type'        => 'video',
            'image'     => self::$image_dir . '/video.png',
            'default'   => [
                'style' => [
                    'backgroundColor' => 'transparent',
                    'padding'         => '15px',
                    'borderWidth'     => '0px',
                    'borderStyle'     => 'solid',
                    'borderColor'     => '#e5e5e5'
                ],
                'textStyle' => [
                    'backgroundColor' => '#333333',
                    'fontSize'        => '14px',
                    'color'           => '#ffffff',
                    'textAlign'       => 'center',
                    'padding'         => '15px',
                ],
                'video' => [
                    'link'           => '',
                    'image'          => '',
                    'alt'            => '',
                    'openAttrEditor' => ''
                ],
                'text'        => sprintf( '<p>%s</p>', __( 'This is a text block. You can use it to add text to your template.', 'wemail' ) ),
                'capPosition' => 'bottom',
            ]
        ];
    }

    public static function footer() {
        return [
            'type'        => 'footer',
            'image'      => self::$image_dir . '/footer.png',
            'default'    => [
                'style'  => [
                    'backgroundColor' => '#ffffff',
                    'paddingTop'      => '15px',
                    'paddingBottom'   => '15px',
                    'paddingLeft'     => '15px',
                    'paddingRight'    => '15px',
                    'borderWidth'     => '0px',
                    'borderStyle'     => 'solid',
                    'borderColor'     => '#e5e5e5'
                ],
                'twoColumns' => false,
                'texts'      => [
                    '<p style="text-align: center;"><span style="font-size: 12px;">This email was sent to [user:email] because you have opted in to receive specific updates on our website.</span></p><p style="text-align: center;"><span style="font-size: 12px;">If you would prefer not to receive any email from us in the future, please [links:unsubscribe text="click here to unsubscribe"] or go to your [links:edit_subscription text="account preferences"] on our website.</span></p><p style="text-align: center;"><span style="font-size: 12px;"><strong>Our mailing address</strong></span><br /><span style="font-size: 12px;"> [company:name]</span><br /><span style="font-size: 12px;"> [company:address]</span></p><p style="text-align: center;"><span style="font-size: 12px;">Copyright © [date:year] [company:name], All rights reserved.</span></p>',
                    sprintf( '<p>%s</p>', __( 'This is a text block. You can use it to add text to your template.', 'wemail' ) )
                ],
                'valign'     => 'top',
            ]
        ];
    }
}
