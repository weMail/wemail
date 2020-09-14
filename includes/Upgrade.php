<?php
namespace WeDevs\WeMail;

class Upgrade {

    protected $upgrades = [
        '1.0.0' => 'Upgrades/upgrade-1.0.0.php',
    ];

    /**
     * Get the plugin version
     *
     * @return string
     */
    protected function get_version() {
        return get_option( 'wemail_version', '0.14.0' );
    }

    /**
     * Check if the plugin needs any update
     *
     * @return bool
     */
    public function needs_update() {

        //check if current version is greater then installed version and any update key is available
        if ( version_compare( $this->get_version(), WEMAIL_VERSION, '<' ) && in_array( WEMAIL_VERSION, array_keys( $this->upgrades ), true ) ) {
            return true;
        }

        return false;
    }

    /**
     * Perform all the necessary upgrade routines
     *
     * @return void
     */
    public function perform_updates() {
        $installed_version = $this->get_version();
        $path = trailingslashit( __DIR__ );

        foreach ( $this->upgrades as $version => $file ) {
            if ( version_compare( $installed_version, $version, '<' ) ) {
                include $path . $file;
                update_option( 'wemail_version', $version );
            }
        }

        update_option( 'wemail_version', WEMAIL_VERSION );
    }

}
