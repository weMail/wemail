<?php

namespace WeDevs\WeMail\FrontEnd;

class Shortcodes {

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        add_shortcode( 'wemail_form', array( $this, 'form' ) );
    }

    /**
     * Shortcode callback
     *
     * @since 1.0.0
     *
     * @param array $attrs
     *
     * @return null|string
     */
    public function form( $attrs ) {
        if ( empty( $attrs['id'] ) ) {
            return __( 'Form Id is missing', 'wemail' );
        }

        $id = $attrs['id'];

        return wemail_form( $id );
    }
}
