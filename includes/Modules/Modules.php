<?php

namespace WeDevs\WeMail\Modules;

class Modules {

    private $modules = [];

    public function __construct() {
        $this->include_modules();
    }

    public function __get( $module_name ) {
        if ( array_key_exists( $module_name, $this->modules ) ) {
            return $this->modules[ $module_name ];
        }

        return $this->{$module_name};
    }

    private function include_modules() {
        $module_dirs = glob( WEMAIL_MODULES . '/*', GLOB_ONLYDIR );

        foreach ( $module_dirs as $module_dir ) {
            $module = str_replace( WEMAIL_MODULES . '/', '', $module_dir );
            $module_name = lcfirst( $module );

            $module_class = "\\WeDevs\\WeMail\\Modules\\$module\\$module";

            $this->set_module( $module_name, $module_class );
        }
    }

    public function set_module( $module_name, $module_class ) {
        $this->modules[ $module_name ] = new $module_class;
    }

}
