<?php

namespace WeDevs\WeMail\FrontEnd;

class Shortcodes {

    public function __construct() {
        add_shortcode( 'wemail_form', [ $this, 'form' ] );
    }

    public function form( $attrs ) {
        if ( empty( $attrs['id'] ) ) {
            return __( 'Form Id is missing', 'wemail' );
        }

        $id = $attrs['id'];

        $form = wemail()->form->get( $id );

        if ( ! $form ) {
            return null;
        }

        unset( $form['entries'] );
        unset( $form['created_at'] );
        unset( $form['deleted_at'] );
        unset( $form['settings']['actions'] );

        ob_start();
        include WEMAIL_VIEWS . '/form.php';
        return ob_get_clean();
    }

}

