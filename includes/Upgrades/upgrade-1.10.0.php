<?php

function add_installed_time() {
    if ( empty( get_option( 'wemail_installed_time' ) ) ) {
        \WeDevs\WeMail\WeMail::instance()->set_wemail_api();
        $site = \WeDevs\WeMail\Core\Api\Api::instance()
            ->auth()->sites(wemail_str_slug(wemail_get_host_from_url(untrailingslashit( site_url( '/' ) )), ''))->get();
        update_option( 'wemail_installed_time', strtotime($site['data']['created_at']) );
    }
}

add_installed_time();
