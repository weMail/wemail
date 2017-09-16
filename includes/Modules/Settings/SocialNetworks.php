<?php

namespace WeDevs\WeMail\Modules\Settings;

use WeDevs\WeMail\Modules\Settings\AbstractSettings;

class SocialNetworks extends AbstractSettings {

    /**
     * Menu priority
     *
     * @since 1.0.0
     *
     * @var integer
     */
    public $priority = 20;

    /**
     * Settings menu name
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_menu_name() {
        return __( 'Social Networks', 'wemail' );
    }

    /**
     * Settings data
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_settings() {
        $defaults = [
            'facebook'      => 'https://facebook.com',
            'twitter'       => 'https://twitter.com',
            'google-plus'   => 'https://plus.google.com/',
            'linkedin'      => 'https://linkedin.com',
            'youtube'       => 'https://youtube.com',
            'instagram'     => 'https://instagram.com',
            'pinterest'     => 'https://pinterest.com',
            'tumblr'        => 'https://tumblr.com',
            'flickr'        => 'https://flickr.com',
            'reddit'        => 'https://reddit.com',
            'snapchat'      => 'https://snapchat.com',
            'whatsapp'      => 'https://whatsapp.com',
            'quora'         => 'https://quora.com',
            'vine'          => 'https://vine.com',
            'periscope'     => 'https://periscope.com',
            'delicious'     => 'https://delicious.com',
            'digg'          => 'https://digg.com',
            'viber'         => 'https://viber.com',
        ];

        $settings = wemail()->api->get( '/settings/social-networks' );

        return wp_parse_args( $settings, $defaults );
    }

    public function order() {
        $order = [
            'facebook',
            'twitter',
            'google-plus',
            'linkedin',
            'youtube',
            'instagram',
            'pinterest',
            'snapchat',
            'whatsapp',
            'tumblr',
            'flickr',
            'reddit',
            'quora',
            'vine',
            'periscope',
            'delicious',
            'digg',
            'viber'
        ];

        /**
         * Social Network handles order
         *
         * @since 1.0.0
         *
         * @param array $order
         */
        return apply_filters( 'wemail_social_networks_order', $order );
    }

    /**
     * Route data
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function get_route_data() {
        return [
            'i18n' => [
                'facebook'     => __( 'Facebook', 'wemail' ),
                'twitter'      => __( 'Twitter', 'wemail' ),
                'linkedin'     => __( 'LinkedIn', 'wemail' ),
                'google-plus'  => __( 'Google+', 'wemail' ),
                'youtube'      => __( 'YouTube', 'wemail' ),
                'pinterest'    => __( 'Pinterest', 'wemail' ),
                'instagram'    => __( 'Instagram', 'wemail' ),
                'tumblr'       => __( 'Tumblr', 'wemail' ),
                'flickr'       => __( 'Flickr', 'wemail' ),
                'reddit'       => __( 'Reddit', 'wemail' ),
                'snapchat'     => __( 'Snapchat', 'wemail' ),
                'whatsapp'     => __( 'WhatsApp', 'wemail' ),
                'quora'        => __( 'Quora', 'wemail' ),
                'vine'         => __( 'Vine', 'wemail' ),
                'periscope'    => __( 'Periscope', 'wemail' ),
                'delicious'    => __( 'Delicious', 'wemail' ),
                'digg'         => __( 'Digg', 'wemail' ),
                'viber'        => __( 'Viber', 'wemail' ),
            ],
            'settings' => $this->get_settings(),
            'order'    => $this->order()
        ];
    }

}
