<?php

namespace WeDevs\WeMail;

class Uninstall {

    public static function uninstall() {
        $data = array(
            'deactivated' => true,
        );

        $api_key    = get_option( 'wemail_api_key' );
        if ($api_key) {
            wemail()->api->site()->update_activation_status()->post( $data );
        }
    }
}
