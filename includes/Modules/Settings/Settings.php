<?php

namespace WeDevs\WeMail\Modules\Settings;

use Stringy\StaticStringy;
use WeDevs\WeMail\Framework\Module;

class Settings extends Module {

    /**
     * Submenu priority
     *
     * @since 1.0.0
     *
     * @var integer
     */
    public $menu_priority = 100;

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
        $this->add_filter( 'wemail_admin_submenu', 'register_submenu', $this->menu_priority, 2 );
        $this->register_settings();

        $this->add_filter( 'wemail_get_route_data_settings', 'get_route_data' );
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

        $this->settings->each(function($settings_class) use ($name, &$settings) {
            if (get_class($settings_class) === "WeDevs\\WeMail\\Modules\\Settings\\$name") {
                $settings = $settings_class->get_settings();
            }
        });

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

        $this->settings->each(function($settings_class) use ($name, &$settings) {
            if (get_class($settings_class) === "WeDevs\\WeMail\\Modules\\Settings\\$name") {
                $settings = $settings_class;
            }
        });

        return $settings ? $settings : $name;
    }

    /**
     * Register submenu
     *
     * @since 1.0.0
     *
     * @param array $menu_items
     * @param string $capability
     *
     * @return array
     */
    public function register_submenu( $menu_items, $capability ) {
        if ( wemail()->user->can( 'manage_settings' ) ) {
            $menu_items[] = [ __( 'Settings', 'wemail' ), $capability, 'admin.php?page=wemail#/settings' ];
        }

        return $menu_items;
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

        foreach ( $file_paths as $file_path ) {
            $file_name = str_replace( WEMAIL_MODULES . '/Settings/', '', $file_path );

            if ( $file_name !== 'Settings.php' && $file_name !== 'AbstractSettings.php' ) {
                $class_name = str_replace( '.php', '', $file_name );
                $class_name = "\\WeDevs\\WeMail\\Modules\\Settings\\$class_name";
                $settings[] = new $class_name();
            }
        }

        $settings = apply_filters( 'wemail_register_settings', $settings );

        $this->settings = collect( $settings )->sortBy( 'priority' );
    }

    /**
     * Settings route data
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_route_data() {
        return [
            'settings' => $this->settings->map( function ( $setting ) {
                return [
                    'name'  => $setting->name,
                    'path'  => $setting->path,
                    'title' => $setting->title
                ];
            } )
        ];
    }

}
