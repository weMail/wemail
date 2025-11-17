<?php

namespace WeDevs\WeMail;

/**
 * Installer class
 */
class Install {

    /**
     * Run the installer
     *
     * @return void
     */
    public static function install() {
        // create tables
        self::create_tables();
        self::add_installed_time();

        // update the current version
        update_option( 'wemail_version', WEMAIL_VERSION );

        // let the API know we are active again
        $api_key    = get_option( 'wemail_api_key' );

        if ($api_key) {
            $wemail_api = wemail()->wemail_api;

            wp_remote_post(
                $wemail_api . '/site/update-activation-status',
                array(
                    'headers' => array(
                        'x-api-key' => $api_key,
                        'x-wemail-user' => wemail()->wemail_api->get_user_email(),
                    ),
                    'body'    => array(
                        'deactivated' => false,
                    ),
                )
            );
        }

        // set the redirection to setup wizard
        set_transient( 'wemail_activation_redirect', true, 30 );
    }

    /**
     * Create the necessary tables
     *
     * @return void
     */
    public static function create_tables() {
        $path      = trailingslashit( __DIR__ );
        $file_name = 'Upgrades/upgrade-1.0.0.php';

        include $path . $file_name;
    }

    /**
     * Adds plugin installation time.
     *
     * @return void
     */
    public static function add_installed_time() {
        $path      = trailingslashit( __DIR__ );
        $file_name = 'Upgrades/upgrade-1.10.0.php';

        include $path . $file_name;
    }
}
