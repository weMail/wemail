<?php

namespace WeDevs\WeMail\Modules\Settings;

use Stringy\StaticStringy;
use WeDevs\WeMail\Framework\Module;

class Settings extends Module {

    /**
     * List of wemail settings
     *
     * @since 1.0.0
     *
     * @var array
     */
    public $settings = [];

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->register_settings();
    }

    /**
     * Magic method to call an wemail setting
     *
     * @since 1.0.0
     *
     * @param string $name
     * @param array  $args
     *
     * @return array
     */
    public function __call( $name, $args ) {
        $name = StaticStringy::upperCamelize($name);

        $settings = null;

        foreach ( $this->settings as $setting ) {
            if (get_class($setting) === "WeDevs\\WeMail\\Modules\\Settings\\$name") {
                return $setting->get_settings();
            }
        }

        return $settings;
    }

    /**
     * Magic method to get an wemail settings class
     *
     * @since 1.0.0
     *
     * @param string $name
     *
     * @return object
     */
    public function __get( $name ) {
        $name = StaticStringy::upperCamelize($name);

        $settings = null;

        foreach ( $this->settings as $setting ) {
            if (get_class($setting) === "WeDevs\\WeMail\\Modules\\Settings\\$name") {
                $settings = $setting;
            }
        }

        return $settings ? $settings : $name;
    }

    /**
     * Register settings
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function register_settings() {
        $file_paths = glob( WEMAIL_MODULES . '/Settings/*.php' );

        $settings = [];

        $ignore_files = ['AbstractSettings.php', 'Routes.php', 'Menu.php'];

        foreach ( $file_paths as $file_path ) {
            $file_name = str_replace( WEMAIL_MODULES . '/Settings/', '', $file_path );

            if ( $file_name !== 'Settings.php' && !in_array( $file_name, $ignore_files ) ) {
                $class_name = str_replace( '.php', '', $file_name );
                $class_name = "\\WeDevs\\WeMail\\Modules\\Settings\\$class_name";
                $settings[] = new $class_name();
            }
        }

        $settings = apply_filters( 'wemail_register_settings', $settings );

        usort( $settings, [ $this, 'sort_by_priority' ] );

        $this->settings = $settings;
    }

    public function sort_by_priority($a, $b) {
        if ($a->priority == $b->priority) {
            return 0;
        }

        return ($a->priority < $b->priority) ? -1 : 1;
    }

}
