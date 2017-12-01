<?php

namespace WeDevs\WeMail\Modules\Customizer;

use WeDevs\WeMail\Modules\Customizer\ContentTypes;

/**
 * Email template customizer framework
 */
class Customizer {

    /**
     * Which customizer we are working on. e.g: campaign, woocommerce, wp etc
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $context = '';

    /**
     * Content types for customizer
     *
     * @since 1.0.0
     *
     * @var array
     */
    public $content_types = [];

    /**
     * Get all the data required for a customizer
     *
     * @since 1.0.0
     *
     * @param string $context
     * @param array  $content_types
     *
     * @return array
     */
    public function get( $context, $content_types ) {
        $this->context = $context;

        /**
         * Filter for weMail customizer content types
         *
         * @since 1.0.0
         *
         * @param array $content_types
         */
        $this->content_types = apply_filters( "wemail_customizer_content_types_{$context}", $content_types );

        // Add website in the social network links
        $company = wemail()->settings->company();
        $social_networks = wemail()->settings->social_networks();
        $social_networks['website'] = $company['website'] ? $company['website'] : 'http://example.com';

        $networks = wemail()->settings->social_networks->networks();
        array_unshift( $networks, 'website' );

        $social_network_title = wemail()->settings->social_networks->title();

        return [
            'contentTypes'     => [
                'types'        => $this->content_types,
                'settings'     => $this->get_content_type_settings()
            ],
            'shortcodes'       => wemail()->shortcode->get(),
            'shortcodeImg'     => WEMAIL_ASSETS . '/images/shortcode.png',
            'placeholderImage' => WEMAIL_ASSETS . '/images/misc/placeholder-image.png',
            'socialNetworks'   => [
                'networks'     => $networks,
                'defaults'     => $social_networks,
                'title'        => $social_network_title,
            ],
            'dividers'         => $this->dividers()
        ];
    }

    /**
     * Settings for content types
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_content_type_settings() {
        $types = ContentTypes::get_content_type_settings( $this->context );

        return array_filter( $types, function ( $type ) {
            return in_array( $type , $this->content_types );
        }, ARRAY_FILTER_USE_KEY );
    }

    /**
     * Divider variation data
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function dividers() {
        $dividers = [
            'baseURL' => WEMAIL_ASSETS . '/images/dividers/',
            'images'  => [
                [ 'name' => 'brush-stroke-lite.png', 'height' => '24px' ],
                [ 'name' => 'brush-stroke-orange.png', 'height' => '24px' ],
                [ 'name' => 'dotted-line.png', 'height' => '24px' ],
                [ 'name' => 'handwritten-swirl-black.png', 'height' => '24px' ],
                [ 'name' => 'handwritten-swirl-cayan.png', 'height' => '24px' ],
                [ 'name' => 'mail-ribbon.png', 'height' => '3px' ],
                [ 'name' => 'ornamental-1.png', 'height' => '24px' ],
                [ 'name' => 'ornamental-2.png', 'height' => '24px' ],
                [ 'name' => 'ornamental-3.png', 'height' => '24px' ],
                [ 'name' => 'shadow-1.png', 'height' => '24px' ],
                [ 'name' => 'shadow-2.png', 'height' => '24px' ],
                [ 'name' => 'star.png', 'height' => '24px' ],
            ]
        ];

        return $dividers;
    }
}
