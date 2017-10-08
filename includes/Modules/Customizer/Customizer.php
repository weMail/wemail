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

        return [
            'i18n'             => $this->i18n(),
            'contentTypes'     => [
                'types'        => $this->content_types,
                'settings'     => $this->get_type_settings()
            ],
            'shortcodes'       => wemail()->shortcode->get(),
            'placeholderImage' => WEMAIL_ASSETS . '/images/misc/placeholder-image.png',
            'socialNetworks'   => [
                'networks'     => $networks,
                'defaults'     => $social_networks
            ]
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
            'padding'             => __( 'Padding', 'wemail' ),
            'paddingTopBottom'    => __( 'Padding Top-Bottom', 'wemail' ),
            'paddingLeftRight'    => __( 'Padding Left-Right', 'wemail' ),
            'border'              => __( 'Border', 'wemail' ),
            'numberOfColumns'     => __( 'Number of columns', 'wemail' ),
            'column1'             => __( 'Column 1', 'wemail' ),
            'column2'             => __( 'Column 2', 'wemail' ),
            'twoColumns'          => __( 'Two columns', 'wemail' ),
            'selectAnImage'       => __( 'Select an image', 'wemail' ),
            'pleaseSelectAnImage' => __( 'Please select an image', 'wemail' ),
            'uploadAnImage'       => __( 'Upload an image', 'wemail' ),
            'replace'             => __( 'Replace', 'wemail' ),
            'link'                => __( 'Link', 'wemail' ),
            'alt'                 => __( 'Alt', 'wemail' ),
            'width'               => __( 'Width', 'wemail' ),
            'remove'              => __( 'Remove', 'wemail' ),
            'addAnotherImage'     => __( 'Add another image', 'wemail' ),
            'oneImageIsRequired'  => __( 'At least one image is required', 'wemail' ),
            'browseImage'         => __( 'Browse Image', 'wemail' ),
            'untitled'            => __( 'untitled', 'wemail' ),
            'imageAlign'          => __( 'Image Align', 'wemail' ),
            'setImageLink'        => __( 'Set Image Link', 'wemail' ),
            'setImageAltText'     => __( 'Set Image Alt Text', 'wemail' ),
            'close'               => __( 'Close', 'wemail' ),
            'bottomMargin'        => __( 'Bottom Margin', 'wemail' ),
            'twoCaptions'         => __( 'Two captions', 'wemail' ),
            'left'                => __( 'Left', 'wemail' ),
            'right'               => __( 'Right', 'wemail' ),
            'top'                 => __( 'Top', 'wemail' ),
            'bottom'              => __( 'Bottom', 'wemail' ),
            'captionPosition'     => __( 'Caption Position', 'wemail' ),
            'caption1'            => __( 'Caption 1', 'wemail' ),
            'caption2'            => __( 'Caption 2', 'wemail' ),
            'addMoreNetwork'      => __( 'Add More Network', 'wemail' ),
            'pageLink'            => __( 'Page Link', 'wemail' ),
            'linkText'            => __( 'Link Text', 'wemail' ),
            'website'             => __( 'Website', 'wemail' ),
            'upperCase'           => __( 'Uppercase', 'wemail' ),
            'yes'                 => __( 'Yes', 'wemail' ),
            'fontWeight'          => __( 'Font Weight', 'wemail' ),
            'normal'              => __( 'Normal', 'wemail' ),
            'bold'                => __( 'Bold', 'wemail' ),
            'fontSize'            => __( 'Font Size', 'wemail' ),
            'iconMargin'          => __( 'Icon Margin', 'wemail' ),
            'display'             => __( 'Display', 'wemail' ),
            'iconOnly'            => __( 'Icon only', 'wemail' ),
            'textOnly'            => __( 'Text only', 'wemail' ),
            'both'                => __( 'Both', 'wemail' ),
            'align'               => __( 'Align', 'wemail' ),
            'center'              => __( 'Center', 'wemail' ),
            'layout'              => __( 'Layout', 'wemail' ),
        ];

        $social_networks = wemail()->settings->social_networks->i18n();

        $i18n = array_merge( $i18n, $social_networks );

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
