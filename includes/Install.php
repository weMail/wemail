<?php
namespace WeDevs\WeMail;

class Install {

    public static function install() {

    }

    public static function status_update() {

        if ( get_option('wemail_plugin_active') ) {
            return;
        }

        wemail()->api->site()->update_activation_status()->post( [
            'deactivated' => false
        ] );

        update_option('wemail_plugin_active', true);
    }

}
