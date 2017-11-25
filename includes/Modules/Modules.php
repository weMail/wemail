<?php

namespace WeDevs\WeMail\Modules;

use Stringy\StaticStringy;

class Modules {

    /**
     * weMail module list
     *
     * @since 1.0.0
     *
     * @var array
     */
    public $modules = [];

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->include_modules();
    }

    /**
     * Magic gettter method to access the module class
     *
     * @since 1.0.0
     *
     * @param string $module_name All lowercase-underscored module name
     *
     * @return object
     */
    public function __get( $module_name ) {
        if ( array_key_exists( $module_name, $this->modules ) ) {
            return $this->modules[ $module_name ];
        }

        return $this->{$module_name};
    }

    /**
     * Include all modules
     *
     * This will include all modules found in includes/Modules directory.
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function include_modules() {
        $module_dirs = glob( WEMAIL_MODULES . '/*', GLOB_ONLYDIR );

        foreach ( $module_dirs as $module_dir ) {
            $module       = str_replace( WEMAIL_MODULES . '/', '', $module_dir );
            $module_name  = StaticStringy::underscored( $module );

            $module_class = "\\WeDevs\\WeMail\\Modules\\$module\\$module";
            $menu_class = "\\WeDevs\\WeMail\\Modules\\$module\\Menu";
            $route_class = "\\WeDevs\\WeMail\\Modules\\$module\\Routes";

            $this->register_module( $module_name, $module_class );
            $this->register_menu( $module_name, $menu_class );
            $this->register_route_hooks( $module_name, $route_class );
        }
    }

    /**
     * Register a module to wemail module list
     *
     * @since 1.0.0
     *
     * @param string $module_name  All lowercase-underscored module name
     * @param string $module_class Module fully qualified name
     *
     * @return void
     */
    public function register_module( $module_name, $module_class ) {
        $this->modules[ $module_name ] = new $module_class;
    }

    /**
     * Register admin menu
     *
     * @since 1.0.0
     *
     * @param string $module_name
     * @param string $menu_class
     *
     * @return void
     */
    public function register_menu( $module_name, $menu_class ) {
        if ( class_exists( $menu_class ) ) {
            new $menu_class();
        }
    }

    /**
     * Register route hooks
     *
     * @since 1.0.0
     *
     * @param string $module_name
     * @param string $route_class
     *
     * @return void
     */
    public function register_route_hooks( $module_name, $route_class ) {
        if ( class_exists( $route_class ) ) {
            new $route_class();
        }
    }

    /**
     * Check if wemail has a registered module
     *
     * @since 1.0.0
     *
     * @param $string $module_name All lowercase-underscored module name
     *
     * @return boolean
     */
    public function has_module( $module_name ) {
        return array_key_exists( $module_name, $this->modules ) || false;
    }

    /**
     * Get wemail registered module names
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_module_names() {
        return array_keys( $this->modules );
    }

}
