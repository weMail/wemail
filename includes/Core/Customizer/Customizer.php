<?php

namespace WeDevs\WeMail\Core\Customizer;

use WeDevs\WeMail\Core\Customizer\ContentTypes;
use WeDevs\WeMail\Traits\Singleton;

/**
 * Email template customizer framework
 */
class Customizer {

    use Singleton;

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

        /**
         * Add website in the social network links
         *
         * $company = wemail()->settings->company();
         * $social_networks = wemail()->settings->social_networks();
         * $social_networks['website'] = $company['website'] ? $company['website'] : 'http://example.com';

         * $networks = wemail()->settings->social_networks->networks();
         * array_unshift( $networks, 'website' );

         * $social_network_title = wemail()->settings->social_networks->title();
         */

        return [
            'siteURL'          => site_url( '/' ),
            'cdn'              => wemail()->wemail_cdn,
            'contentTypes'     => [
                'types'        => $this->content_types,
                'settings'     => $this->get_content_type_settings(),
            ],
            'shortcodes'       => wemail()->shortcode->get(),
            'shortcodeImg'     => wemail()->wemail_cdn . '/images/shortcode.png',
            'placeholderImage' => wemail()->wemail_cdn . '/images/placeholder-image.png',
            'dividers'         => $this->dividers(),
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
        $content_types = ContentTypes::get_content_type_settings( $this->context );

        return array_filter(
            $content_types,
            function ( $content_type ) {
                return in_array( $content_type['type'], $this->content_types, true );
            }
        );
    }

    /**
     * Divider variation data
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function dividers() {
        return [
            'baseURL' => wemail()->wemail_cdn . '/images/dividers/',
            'images'  => [
                [
                    'name' => 'brush-stroke-lite.png',
                    'height' => '24px',
                ],
                [
                    'name' => 'brush-stroke-orange.png',
                    'height' => '24px',
                ],
                [
                    'name' => 'dotted-line.png',
                    'height' => '24px',
                ],
                [
                    'name' => 'handwritten-swirl-black.png',
                    'height' => '24px',
                ],
                [
                    'name' => 'handwritten-swirl-cayan.png',
                    'height' => '24px',
                ],
                [
                    'name' => 'mail-ribbon.png',
                    'height' => '3px',
                ],
                [
                    'name' => 'ornamental-1.png',
                    'height' => '24px',
                ],
                [
                    'name' => 'ornamental-2.png',
                    'height' => '24px',
                ],
                [
                    'name' => 'ornamental-3.png',
                    'height' => '24px',
                ],
                [
                    'name' => 'shadow-1.png',
                    'height' => '24px',
                ],
                [
                    'name' => 'shadow-2.png',
                    'height' => '24px',
                ],
                [
                    'name' => 'star.png',
                    'height' => '24px',
                ],
            ],
        ];
    }
}
