<?php

namespace WeDevs\WeMail\Modules\Customizer;

class ContentTypes {

    /**
     * Content type image directory
     *
     * @since 1.0.0
     *
     * @var string
     */
    public static $image_dir;

    /**
     * Get content type settings for a customizer
     *
     * @since 1.0.0
     *
     * @param string $context Which customizer we are working on. e.g: campaign, woocommerce, wp etc
     *
     * @return array
     */
    public static function get_type_settings( $context ) {
        self::$image_dir = WEMAIL_ASSETS . '/images/content-types';

        $settings = [
            'text'          => self::text(),
            'image'         => self::image(),
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

    /**
     * Content type: Text
     *
     * @since 1.0.0
     *
     * @return array
     */
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
                    'borderColor'     => '#e5e5e5',
                    'marginBottom'    => '0px',
                ],
                'twoColumns' => true,
                'texts'      => [
                    sprintf( '<p>%s</p>', __( 'This is a text block. You can use it to add text to your template.', 'wemail' ) ),
                    sprintf( '<p>%s</p>', __( 'This is a text block 2. You can use it to add text to your template.', 'wemail' ) )
                ],

                'valign'     => 'top',
            ],
            'noSettingsTab' => true
        ];
    }

    /**
     * Content type: Image
     *
     * @since 1.0.0
     *
     * @return array
     */
    public static function image() {
        return [
            'type'    => 'image',
            'image'   => self::$image_dir . '/image.png',
            'default' => [
                'style'  => [
                    'backgroundColor' => '#ffffff',
                    'paddingTop'      => '15px',
                    'paddingBottom'   => '15px',
                    'paddingLeft'     => '15px',
                    'paddingRight'    => '15px',
                    'borderWidth'     => '0px',
                    'borderStyle'     => 'solid',
                    'borderColor'     => '#e5e5e5',
                    'marginBottom'    => '0px',
                ],
                'images' => []
            ],
            'noSettingsTab' => true
        ];
    }

    /**
     * Content type: Image Caption
     *
     * @since 1.0.0
     *
     * @return array
     */
    public static function image_caption() {
        return [
            'type'        => 'imageCaption',
            'image'       => self::$image_dir . '/image-caption.png',
            'default'     => [
                'style'       => [
                    'backgroundColor'       => '#ffffff',
                    'color'                 => '',
                    'verticalPadding'       => '15px',
                    'horizontalPadding'     => '15px',
                    'borderWidth'           => '0px',
                    'borderColor'           => '#e5e5e5',
                    'marginBottom'          => '0px',
                    'text'  => [
                        'paddingTop'        => '0px',
                        'paddintRight'      => '0px',
                        'paddintBottom'     => '0px',
                        'paddintLeft'       => '0px',
                    ]
                ],
                'captions'    => [
                    [
                        'text' => sprintf( '<p>%s</p>', __( 'This is a text block. You can use it to add text to your template.', 'wemail' ) ),
                        'image' => [
                            'alt' => '',
                            'src' => '',
                            'link' => '',
                            'openAttrEditor' => '',
                        ]
                    ],
                    [
                        'text' => sprintf( '<p>%s</p>', __( 'This is a text block. You can use it to add text to your template.', 'wemail' ) ),
                        'image' => [
                            'alt' => '',
                            'src' => '',
                            'link' => '',
                            'openAttrEditor' => '',
                        ]
                    ],
                ],
                'twoCaptions' => false,
                'capPosition' => 'bottom',
            ]
        ];
    }

    /**
     * Content type: Social Follow
     *
     * @since 1.0.0
     *
     * @return array
     */
    public static function social_follow() {
        $company = wemail()->settings->company();
        $social_networks = wemail()->settings->social_networks();

        return [
            'type'       => 'socialFollow',
            'image'      => self::$image_dir . '/social-follow.png',
            'default'    => [
                'style'      => [
                    'backgroundColor'       => '#ffffff',
                    'color'                 => '',
                    'padding'               => '15px',
                    'borderWidth'           => '0px',
                    'borderColor'           => '#e5e5e5',
                    'fontSize'              => '14px',
                    'fontWeight'            => 'normal',
                    'textTransform'         => 'none',
                    'textAlign'             => 'center',
                    'marginBottom'          => '0px',
                ],
                'iconStyle'  => 'solid-color',
                'iconMargin' => '15px',
                'display'    => 'both', // icon/text/both
                'layout'     => 'horizontal', // vertical/horizontal
                'size'       => 'default', // default/large
                'icons'      => [
                    [
                        'site' => 'facebook',
                        'link' => $social_networks['facebook'],
                        'text' => 'Facebook',
                    ],
                    [
                        'site' => 'twitter',
                        'link' => $social_networks['twitter'],
                        'text' => 'Twitter',
                    ],
                    [
                        'site' => 'website',
                        'link' => $company['website'] ? $company['website'] : 'http://example.com',
                        'text' => 'Website'
                    ]
                ],
            ]
        ];
    }

    /**
     * Content type: Button
     *
     * @since 1.0.0
     *
     * @return array
     */
    public static function button() {
        return [
            'type'      => 'button',
            'image'     => self::$image_dir . '/button.png',
            'default'   => [
                'style' => [
                    'display'         => 'inline-block',
                    'paddingTop'      => '18px',
                    'paddingRight'    => '65px',
                    'paddingBottom'   => '18px',
                    'paddingLeft'     => '65px',
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
                'href'           => site_url('/'),
                'title'          => '',
                'containerStyle' => [
                    'textAlign'       => 'center',
                    'backgroundColor' => '#ffffff',
                    'padding'         => '18px',
                    'marginBottom'    => '0px'
                ]
            ]
        ];
    }

    /**
     * Content type: Divider
     *
     * @since 1.0.0
     *
     * @return array
     */
    public static function divider() {
        return [
            'type'            => 'divider',
            'image'           => self::$image_dir . '/divider.png',
            'default'         => [
                'dividerType'    => 'line',
                'containerStyle' => [
                    'padding'         => '15px',
                    'backgroundColor' => '#ffffff',
                    'marginBottom'    => '0px'
                ],
                'style' => [
                    'width'           => '570px',
                    'borderTopWidth'  => '2px',
                    'borderTopStyle'  => 'dashed',
                    'borderTopColor'  => '#e5e5e5',
                    'margin'          => '0 auto'
                ],
                'image'     => [
                    'image' => WEMAIL_ASSETS . '/images/dividers/brush-stroke-lite.png',
                    'style' => [
                        'height' => '7px',
                        'width'  => '600px',
                    ]
                ]
            ],
            'noStyleTab' => true,
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
