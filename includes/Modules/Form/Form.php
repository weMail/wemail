<?php

namespace WeDevs\WeMail\Modules\Form;

use WeDevs\WeMail\Framework\Module;

class Form extends Module {

    public $menu_priority = 80;

    public function __construct() {
        $this->add_filter( 'wemail_admin_submenu', 'register_submenu', $this->menu_priority, 2 );

        $this->add_filter( 'wemail_component_actions', 'add_component_actions' );
    }

    public function register_submenu( $menu_items, $capability ) {
        $menu_items[] = [ __( 'Forms', 'wemail' ), $capability, 'admin.php?page=wemail#/forms' ];

        return $menu_items;
    }

    public function get_route_data_forms() {
        $data = [
            'modelA' => 'Model A data from Home.php ' . current_time( 'mysql' ),
            'notInStore' => 'not found in store'
        ];

        $this->send_success( $data );
    }

    public function add_component_actions( $actions ) {
        $actions['before-forms'][] = [
            'tag' => 'foo'
        ];

        $actions['after-forms'][] = [
            'tag' => 'div',
            'content' => 'hello action {{ parentData }}'
        ];

        $actions['after-forms'][] = [
            'tag' => 'bar'
        ];

        return $actions;
    }

}
