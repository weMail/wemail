<?php

namespace WeDevs\WeMail\Modules\Forms;

use WeDevs\WeMail\Framework\Module;

class Forms extends Module {

    public $route_name = 'forms';
    public $menu_priority = 80;

    public function register_submenu( $menu_items, $capability ) {
        $menu_items[] = [ __( 'Forms', 'wemail' ), $capability, 'admin.php?page=wemail#/forms' ];

        return $menu_items;
    }

    public function register_route( $routes ) {
        $routes[] = [
            'path' => '/forms',
            'name' => 'forms',
            'component' => 'Forms',
            'requires' => WEMAIL_ASSETS . '/js/Forms.js',
            'dependencies' => apply_filters( 'wemail-admin-route-dep-forms', [] ),
            'scrollTo' => 'top'
        ];

        return $routes;
    }

    public function get_forms_initial_data() {
        $data = [
            'modelA' => 'Model A data from Home.php ' . current_time( 'mysql' ),
            'notInStore' => 'not found in store'
        ];

        $this->send_success( $data );
    }

}
