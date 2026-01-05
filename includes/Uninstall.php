<?php

namespace WeDevs\WeMail;

class Uninstall {

    public static function uninstall() {
        $data = array(
            'deactivated' => true,
        );

        $api_key    = get_option( 'wemail_api_key' );
        if ($api_key) {
            try {
                wemail()->api->site()->update_activation_status()->post( $data );
            } catch (\Exception $exception) {
                // silence is golden
            }
        }
    }
}
