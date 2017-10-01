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
        $this->context       = $context;

        /**
         * Filter for weMail customizer content types
         *
         * @since 1.0.0
         *
         * @param array $content_types
         */
        $this->content_types = apply_filters( "wemail_customizer_content_types_{$context}", $content_types );

        return [
            'i18n'         => $this->i18n(),
            'contentTypes' => [
                'types'    => $this->content_types,
                'settings' => $this->get_type_settings()
            ],
            'shortcodes'   => wemail()->shortcode->get()
        ];
    }

    /**
     * Settings for content types
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_type_settings() {
        $types = ContentTypes::get_type_settings( $this->context );

        return array_filter( $types, function ( $type ) {
            return in_array( $type , $this->content_types );
        }, ARRAY_FILTER_USE_KEY );
    }

    /**
     * i18n strings for customizer
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function i18n() {
        $i18n = [
            'text'                => __( 'Text', 'wemail' ),
            'image'               => __( 'Image', 'wemail' ),
            'imageGroup'          => __( 'Image Group', 'wemail' ),
            'imageCaption'        => __( 'Image Caption', 'wemail' ),
            'socialFollow'        => __( 'Social Follow', 'wemail' ),
            'button'              => __( 'Button', 'wemail' ),
            'divider'             => __( 'Divider', 'wemail' ),
            'video'               => __( 'Video', 'wemail' ),
            'footer'              => __( 'Footer', 'wemail' ),
            'content'             => __( 'Content', 'wemail' ),
            'design'              => __( 'Design', 'wemail' ),
            'style'               => __( 'Style', 'wemail' ),
            'settings'            => __( 'Settings', 'wemail' ),
            'saveAndClose'        => __( 'Save & close', 'wemail' ),
            'backgroundColor'     => __( 'Background Color', 'wemail' ),
            'fontColor'           => __( 'Font Color', 'wemail' ),
            'paddingTopBottom'    => __( 'Padding Top-Bottom', 'wemail' ),
            'paddingLeftRight'    => __( 'Padding Left-Right', 'wemail' ),
            'border'              => __( 'Border', 'wemail' ),
            'numberOfColumns'     => __( 'Number of columns', 'wemail' ),
            'column1'             => __( 'Column 1', 'wemail' ),
            'column2'             => __( 'Column 2', 'wemail' ),
            'twoColumns'          => __( 'Two columns', 'wemail' ),
            'selectAnImage'       => __( 'Select an image', 'wemail' ),
            'pleaseSelectAnImage' => __( 'Please select an image', 'wemail' ),
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
