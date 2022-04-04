<?php

    function add_installed_time() {
        if ( empty( get_option( 'wemail_installed_time' ) ) ) {
            $current_time = time();
            update_option( 'wemail_installed_time', $current_time );
        }
    }
    add_installed_time();
?>
