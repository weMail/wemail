<?php

namespace WeDevs\WeMail\Core\Customizer;

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
    public static function get_content_type_settings( $context ) {
        self::$image_dir = wemail()->wemail_cdn . '/images/content-types';

        $settings = array(
            'text'          => self::text(),
            'image'         => self::image(),
            'imageCaption'  => self::image_caption(),
            'socialFollow'  => self::social_follow(),
            'button'        => self::button(),
            'divider'       => self::divider(),
            'video'         => self::video(),
            'footer'        => self::footer(),
            'countdown'     => self::countdown(),
            'wooProducts'   => self::woo_commerce_product(),
            'giphy'         => self::giphy(),
        );

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
        return array(
            'type'       => 'text',
            'title'      => __( 'Text', 'wemail' ),
            'image'      => self::$image_dir . '/text.svg',
            'default'    => array(
                'style'      => array(
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
                ),
                'twoColumns' => false,
                'texts'      => array(
                    sprintf( '<p>%s</p>', __( 'This is a text block. You can use it to add text to your template.', 'wemail' ) ),
                    sprintf( '<p>%s</p>', __( 'This is a text block 2. You can use it to add text to your template.', 'wemail' ) ),
                ),
                'columnSplit' => '1-1', // 1-1, 1-2, 2-1
                'valign'     => 'top',
            ),
        );
    }

    /**
     * Content type: Image
     *
     * @since 1.0.0
     *
     * @return array
     */
    public static function image() {
        return array(
            'type'    => 'image',
            'title'   => __( 'Image', 'wemail' ),
            'image'   => self::$image_dir . '/image.svg',
            'default' => array(
                'style'  => array(
                    'backgroundColor' => '#ffffff',
                    'paddingTop'      => '15px',
                    'paddingBottom'   => '15px',
                    'paddingLeft'     => '15px',
                    'paddingRight'    => '15px',
                    'borderWidth'     => '0px',
                    'borderStyle'     => 'solid',
                    'borderColor'     => '#e5e5e5',
                    'marginBottom'    => '0px',
                    'textAlign'       => 'center',
                ),
                'images' => array(
                    array(
                        'alt' => '',
                        'src' => wemail()->wemail_cdn . '/images/placeholder-image-full.png',
                        'link' => '',
                        'openAttrEditor' => '',
                    ),
                ),
            ),
        );
    }

    /**
     * Content type: Image Caption
     *
     * @since 1.0.0
     *
     * @return array
     */
    public static function image_caption() {
        return array(
            'type'        => 'imageCaption',
            'title'       => __( 'Image Caption', 'wemail' ),
            'image'       => self::$image_dir . '/image-caption.svg',
            'default'     => array(
                'style'       => array(
                    'backgroundColor'       => '#ffffff',
                    'color'                 => '',
                    'padding'               => '15px',
                    'borderWidth'           => '0px',
                    'borderColor'           => '#e5e5e5',
                    'marginBottom'          => '0px',
                    'textAlign'             => 'center',
                    'text'  => array(
                        'paddingTop'        => '0px',
                        'paddintRight'      => '0px',
                        'paddintBottom'     => '0px',
                        'paddintLeft'       => '0px',
                    ),
                ),
                'captions'    => array(
                    array(
                        'text' => sprintf( '<p>%s</p>', __( 'This is a text block. You can use it to add text to your template.', 'wemail' ) ),
                        'image' => array(
                            'alt' => '',
                            'src' => wemail()->wemail_cdn . '/images/placeholder-image-full.png',
                            'link' => '',
                            'openAttrEditor' => '',
                        ),
                    ),
                    array(
                        'text' => sprintf( '<p>%s</p>', __( 'This is a text block. You can use it to add text to your template.', 'wemail' ) ),
                        'image' => array(
                            'alt' => '',
                            'src' => wemail()->wemail_cdn . '/images/placeholder-image-full.png',
                            'link' => '',
                            'openAttrEditor' => '',
                        ),
                    ),
                ),
                'twoCaptions' => false,
                'capPosition' => 'bottom',
            ),
        );
    }

    /**
     * Content type: Social Follow
     *
     * @since 1.0.0
     *
     * @return array
     */
    public static function social_follow() {
        $settings = wemail()->api->settings()->social_networks()->get();
        $company = wemail()->api->settings()->company()->get();

        $facebook = 'https://facebook.com';
        $x  = 'https://x.com';
        $website  = 'https://example.com';

        if ( ! empty( $settings['data'] ) ) {
            $facebook = $settings['data']['facebook'];
            $x = $settings['data']['twitter'];
        }

        if ( ! empty( $company['data'] ) ) {
            $website = $company['data']['website'];
        }

        return array(
            'type'       => 'socialFollow',
            'title'      => __( 'Social Follow', 'wemail' ),
            'image'      => self::$image_dir . '/social-follow.svg',
            'default'    => array(
                'style'      => array(
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
                ),
                'iconStyle'  => 'solid-color',
                'iconMargin' => '15px',
                'display'    => 'both', // icon/text/both
                'layout'     => 'horizontal', // vertical/horizontal
                'size'       => 'default', // default/large
                'icons'      => array(
                    array(
                        'site' => 'facebook',
                        'link' => $facebook,
                        'text' => 'Facebook',
                    ),
                    array(
                        'site' => 'X',
                        'link' => $x,
                        'text' => 'X',
                    ),
                    array(
                        'site' => 'website',
                        'link' => $website,
                        'text' => 'Website',
                    ),
                ),
            ),
        );
    }

    /**
     * Content type: Button
     *
     * @since 1.0.0
     *
     * @return array
     */
    public static function button() {
        return array(
            'type'      => 'button',
            'title'     => __( 'Button', 'wemail' ),
            'image'     => self::$image_dir . '/button.svg',
            'default'   => array(
                'buttons' => array(
                    array(
                        'style' => array(
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
                            'borderColor'     => '#e5e5e5',
                        ),
                        'text'                => __( 'Button Text', 'wemail' ),
                        'href'                => site_url( '/' ),
                        'title'               => '',
                    ),
                ),
                'containerStyle' => array(
                    'textAlign'       => 'center',
                    'backgroundColor' => '#ffffff',
                    'padding'         => '18px',
                    'marginBottom'    => '0px',
                ),
            ),
        );
    }

    /**
     * Content type: Divider
     *
     * @since 1.0.0
     *
     * @return array
     */
    public static function divider() {
        return array(
            'type'            => 'divider',
            'title'           => __( 'Divider', 'wemail' ),
            'image'           => self::$image_dir . '/divider.svg',
            'default'         => array(
                'dividerType'    => 'line',
                'containerStyle' => array(
                    'padding'         => '15px',
                    'backgroundColor' => '#ffffff',
                    'marginBottom'    => '0px',
                ),
                'style' => array(
                    'width'           => '100%',
                    'borderTopWidth'  => '2px',
                    'borderTopStyle'  => 'dashed',
                    'borderTopColor'  => '#e5e5e5',
                    'margin'          => '0 auto',
                ),
                'image'     => array(
                    'image' => wemail()->wemail_cdn . '/images/dividers/brush-stroke-lite.png',
                    'style' => array(
                        'height' => '7px',
                        'width'  => '100%',
                    ),
                ),
            ),
            'noStyleTab' => true,
        );
    }

    public static function video() {
        return array(
            'type'      => 'video',
            'title'     => __( 'Video', 'wemail' ),
            'image'     => self::$image_dir . '/video.svg',
            'default'   => array(
                'style' => array(
                    'backgroundColor' => 'transparent',
                    'padding'         => '15px',
                    'borderWidth'     => '0px',
                    'borderStyle'     => 'solid',
                    'borderColor'     => '#e5e5e5',
                    'textAlign'       => 'center',
                ),
                'textStyle' => array(
                    'backgroundColor' => '#333333',
                    'fontSize'        => '14px',
                    'color'           => '#ffffff',
                    'textAlign'       => 'center',
                    'padding'         => '15px',
                ),
                'video' => array(
                    'link'           => '',
                    'image'          => '',
                    'alt'            => '',
                ),
                'text'        => sprintf( '<p>%s</p>', __( 'This is a text block. You can use it to add text to your template.', 'wemail' ) ),
                'capPosition' => 'bottom',
            ),
        );
    }

    public static function footer() {
        return array(
            'type'       => 'footer',
            'title'      => __( 'Footer', 'wemail' ),
            'image'      => self::$image_dir . '/footer.svg',
            'default'    => array(
                'style'      => array(
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
                ),
                'twoColumns' => false,
                'texts'      => array(
                    '<p style="text-align: center;"><span style="font-size: 12px;">This email was sent to [subscriber:email] because you have opted in to receive specific updates on our website.</span></p><p style="text-align: center;"><span style="font-size: 12px;">If you would prefer not to receive any email from us in the future, please [links:unsubscribe text="click here to unsubscribe"].</span></p><p style="text-align: center;"><span style="font-size: 12px;"><strong>Our mailing address</strong></span><br /><span style="font-size: 12px;"> [company:name]</span><br /><span style="font-size: 12px;"> [company:address]</span></p><p style="text-align: center;"><span style="font-size: 12px;">Copyright © [date:year] [company:name], All rights reserved.</span></p>',
                    sprintf( '<p>%s</p>', __( 'This is a text block. You can use it to add text to your template.', 'wemail' ) ),
                ),
                'columnSplit' => '1-1',
                'valign'     => 'top',
            ),
        );
    }

    /**
     * Content Type: Countdown
     *
     * @return array
     */
    public static function countdown() {
        $api_url = parse_url( wemail()->wemail_api );

        return array(
            'type'       => 'countdown',
            'title'      => __( 'Countdown', 'wemail' ),
            'image'      => self::$image_dir . '/countdown.svg',
            'default'    => array(
                'containerStyle' => array(
                    'textAlign'       => 'center',
                    'backgroundColor' => '#ffffff',
                    'padding'         => '18px',
                    'marginBottom'    => '0px',
                ),
                'label'      => array(
                    'days'      => 'Days',
                    'hours'     => 'Hours',
                    'minutes'   => 'Minutes',
                    'seconds'   => 'Seconds',
                ),
                'query' => array(
                    'date'      => '',
                    'time'      => '',
                    'weight'    => 'regular',
                    'timezone'  => '',
                ),
                'isLabelShow'   => false,
                'fontWeights'   => array(
                    'light'    => __( 'Light', 'wemail' ),
                    'regular'  => __( 'Regular', 'wemail' ),
                    'semibold' => __( 'Semibold', 'wemail' ),
                    'bold'     => __( 'Bold', 'wemail' ),
                ),
                'imageBasePath' => $api_url['scheme'] . '://' . $api_url['host'],
                'timezones' => timezone_identifiers_list(),
            ),
            'noStyleTab'       => true,
        );
    }

    /**
     * Content Type: Woo Commerce Product
     *
     * @return array
     */
    public static function woo_commerce_product() {
        return array(
            'type'      => 'wooProducts',
            'title'     => __( 'Woo Products', 'wemail' ),
            'image'     => self::$image_dir . '/woo-commerce-product.svg',
            'default'   => array(
                'products'         => array(),
                'rowStyle'         => array(
                    'options'      => array(
                        array(
                            'title' => __( 'Grid', 'wemail' ),
                            'value' => 'grid',
                        ),
                        array(
                            'title' => __( 'List', 'wemail' ),
                            'value' => 'list',
                        ),
                    ),
                    'value'        => 'list',
                ),
                'containerStyle'   => array(
                    'textAlign'       => 'left',
                    'backgroundColor' => '#ffffff',
                    'padding'         => '18px',
                    'marginBottom'    => '0px',
                    'color'           => '#2c3e50',
                ),
                'list' => array(
                    'imageWidth' => '40%',
                ),
                'grid' => array(
                    'products' => 2,
                ),
                'content' => array(
                    'valign' => 'top',
                ),
                'product' => array(
                    'image' => array(
                        'padding'           => '0px',
                        'borderColor'       => '#0073aa',
                        'borderWidth'       => '0px',
                        'borderStyle'       => 'solid',
                        'borderRadius'      => '0px',
                        'backgroundColor'   => '#fff',
                        'marginBottom'      => '12px',
                        'maxWidth'          => '100%',
                    ),
                    'borderSpacingTopBottom' => '20',
                    'borderSpacingLeftRight' => '20',
                    'star' => array(
                        'isShow'       => 'on',
                        'color'        => '#fbd233',
                        'size'         => '18px',
                        'marginBottom' => '3px',
                    ),
                    'description' => array(
                        'show' => 'on',
                        'mode'  => 'description',
                        'style' => array(
                            'color'         => '#4F5055',
                            'fontSize'      => '14px',
                            'marginBottom'  => '8px',
                            'textTransform' => 'initial',
                        ),
                    ),
                    'title' => array(
                        'style' => array(
                            'fontSize'       => '20px',
                            'color'          => '#4F5055',
                            'textTransform' => 'initial',
                            'marginBottom'   => '5px',
                        ),
                    ),
                    'price' => array(
                        'style' => array(
                            'color'         => '#4F5055',
                            'fontSize'      => '14px',
                            'marginBottom'  => '0px',
                        ),
                    ),
                    'button' => array(
                        'text'  => __( 'Read More', 'wemail' ),
                        'style' => array(
                            'color'             => '#fff',
                            'backgroundColor'   => '#1E73BE',
                            'fontSize'          => '14px',
                            'borderStyle'       => 'solid',
                            'borderWidth'       => '0px',
                            'borderColor'       => '#0073aa',
                            'borderRadius'      => '3px',
                            'paddingTop'        => '6px',
                            'paddingBottom'     => '6px',
                            'paddingLeft'       => '13px',
                            'paddingRight'      => '13px',
                            'display'           => 'inline-block',
                            'textTransform'     => 'initial',
                            'marginBottom'      => '5px',
                        ),
                    ),
                ),
            ),
        );
    }

    /**
     * Content Type: Giphy
     *
     * @return array
     */
    public static function giphy() {
        return array(
            'type'      => 'giphy',
            'title'     => __( 'Giphy', 'wemail' ),
            'image'     => self::$image_dir . '/giphy.svg',
            'default'   => array(
                'image' => (object) array(),
                'size'  => 'fixed_height_small',
                'style' => array(
                    'borderStyle' => 'solid',
                    'borderWidth' => '0px',
                    'borderColor' => '#eee',
                    'paddingTop' => '5px',
                    'paddingBottom' => '5px',
                    'paddingLeft' => '5px',
                    'paddingRight' => '5px',
                    'backgroundColor' => '#fff',
                    'maxWidth' => '100%',
                ),
                'containerStyle' => array(
                    'textAlign'       => 'center',
                    'backgroundColor' => '#ffffff',
                    'marginBottom'    => '0px',
                ),
            ),
            'noSettingsTab' => true,
        );
    }
}
