<?php

namespace WeDevs\WeMail\Modules\Customizer;

use WeDevs\WeMail\Modules\Customizer\ContentTypes;

/**
 * Email template customizer framework
 */
class Customizer {

    public $context = '';

    public $content_types = [];

    public function __construct() {}

    public function get( $context, $content_types ) {
        $this->context       = $context;
        $this->content_types = apply_filters( "wemail_customizer_content_types_{$context}", $content_types );

        return [
            'i18n'         => $this->i18n(),
            'contentTypes' => [
                'types'    => $this->content_types,
                'settings' => $this->get_type_settings()
            ]
        ];
    }

    public function get_type_settings() {
        $types = ContentTypes::get_type_settings( $this->context );

        return array_filter( $types, function ( $type ) {
            return in_array( $type , $this->content_types );
        }, ARRAY_FILTER_USE_KEY );
    }

    public function i18n() {
        $i18n = [
            'text'          => __( 'Text', 'wemail' ),
            'image'         => __( 'Image', 'wemail' ),
            'imageGroup'    => __( 'Image Group', 'wemail' ),
            'imageCaption'  => __( 'Image Caption', 'wemail' ),
            'socialFollow'  => __( 'Social Follow', 'wemail' ),
            'button'        => __( 'Button', 'wemail' ),
            'divider'       => __( 'Divider', 'wemail' ),
            'video'         => __( 'Video', 'wemail' ),
            'footer'        => __( 'Footer', 'wemail' ),
            'content'       => __( 'Content', 'wemail' ),
            'design'        => __( 'Design', 'wemail' )
        ];

        /**
         * Filter for weMail customizer i18n strings
         *
         * $context - which customizer we are working on. e.g: campaign, woocommerce, wp etc
         *
         * @since 1.0.0
         *
         * @param array $i18n i18n strings
         */
        return apply_filters( "wemail_customizer_i18n_{$this->context}", $i18n );
    }
}
