<?php

namespace WeDevs\WeMail;

class Uninstall {

    public static function uninstall() {
        $data = array(
            'deactivated' => true,
        );

        wemail()->api->site()->update_activation_status()->post( $data );
    }
}
