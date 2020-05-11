<?php
namespace WeDevs\WeMail;

class Install {

    public static function install() {
        $api_key = get_user_meta( get_current_user_id(), 'wemail_api_key', true );
        $api = apply_filters( 'wemail_api_url', 'https://api.getwemail.io/v1' );
        $wemail_api = untrailingslashit( $api );

        wp_remote_post( $wemail_api . '/site/update-activation-status', [
            'headers'     => [
                'x-api-key' => $api_key,
            ],
            'body'        => [
                'deactivated' => false
            ]
        ]);
    }
}
