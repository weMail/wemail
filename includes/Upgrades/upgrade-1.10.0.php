<?php

function wemail_add_installed_time() {
    if ( empty( get_option( 'wemail_installed_time' ) ) ) {
        \WeDevs\WeMail\WeMail::instance()->set_wemail_api();
        $site = \WeDevs\WeMail\Core\Api\Api::instance()
            ->auth()->sites( get_option( 'wemail_site_slug' ) )->get();

        if ( ! is_wp_error( $site ) && is_array( $site ) ) {
            update_option( 'wemail_installed_time', strtotime( $site['data']['created_at'] ) );
        } else {
            update_option( 'wemail_installed_time', time() );
        }
    }
}

wemail_add_installed_time();
