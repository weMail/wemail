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
            [
                'name' => 'facebook',
                'url'  => 'https://facebook.com'
            ],
            [
                'name' => 'twitter',
                'url'  => 'https://twitter.com'
            ],
            [
                'name' => 'linkedin',
                'url'  => 'https://linkedin.com'
            ],
            [
                'name' => 'googleplus',
                'url'  => 'https://plus.google.com/'
            ],
            [
                'name' => 'youtube',
                'url'  => 'https://youtube.com'
            ],
            [
                'name' => 'pinterest',
                'url'  => 'https://pinterest.com'
            ],
            [
                'name' => 'instagram',
                'url'  => 'https://instagram.com'
            ],
            [
                'name' => 'tumblr',
                'url'  => 'https://tumblr.com'
            ],
            [
                'name' => 'flickr',
                'url'  => 'https://flickr.com'
            ],
            [
                'name' => 'reddit',
                'url'  => 'https://reddit.com'
            ],
            [
                'name' => 'snapchat',
                'url'  => 'https://snapchat.com'
            ],
            [
                'name' => 'whatsapp',
                'url'  => 'https://whatsapp.com'
            ],
            [
                'name' => 'quora',
                'url'  => 'https://quora.com'
            ],
            [
                'name' => 'vine',
                'url'  => 'https://vine.com'
            ],
            [
                'name' => 'periscope',
                'url'  => 'https://periscope.com'
            ],
            [
                'name' => 'delicious',
                'url'  => 'https://delicious.com'
            ],
            [
                'name' => 'digg',
                'url'  => 'https://digg.com'
            ],
            [
                'name' => 'viber',
                'url'  => 'https://viber.com'
            ]
        ];

        $settings = wemail()->api->get( '/settings/social-networks' );

        return ! empty( $settings ) ? $settings : $defaults;
    }

    /**
     * Route data
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function get_route_data() {
        $this->verify_nonce();

        $root = [
            'settingsTitle' => $this->menu,
            'i18n'          => $this->parent->i18n(),
        ];

        $current = [
            'i18n' => [
                'facebook'     => __( 'Facebook', 'wemail' ),
                'twitter'      => __( 'Twitter', 'wemail' ),
                'linkedin'     => __( 'LinkedIn', 'wemail' ),
                'googleplus'   => __( 'Google+', 'wemail' ),
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
            'settings' => $this->get_settings()
        ];

        $this->send_success([
            'settings'       => $root,
            'socialNetworks' => $current
        ]);
    }

}
