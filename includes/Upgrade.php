<?php
namespace WeDevs\WeMail;

class Upgrade {

    /**
     * Lists of upgrades
     *
     * @var string[] $upgrades
     */
    protected $upgrades = [
        '1.0.0' => 'Upgrades/upgrade-1.0.0.php',
    ];

    /**
     * WeMail version option key
     *
     * @var string $option_name
     */
    protected $option_name = 'wemail_version';

    /**
     * Get the plugin version
     *
     * @return string
     */
    protected function get_version() {
        return get_option( $this->option_name, '0.14.0' );
    }

    /**
     * Check if the plugin needs any update
     *
     * @return bool
     */
    public function needs_update() {
        // check if current version is greater then installed version
        return version_compare( $this->get_version(), WEMAIL_VERSION, '<' );
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
                update_option( $this->option_name, $version );
            }
        }

        update_option( $this->option_name, WEMAIL_VERSION );
    }

}
