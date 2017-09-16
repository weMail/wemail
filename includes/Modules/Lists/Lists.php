<?php

namespace WeDevs\WeMail\Modules\Lists;

use WeDevs\WeMail\Framework\Module;

class Lists extends Module {

    public $menu_priority = 90;

    public function __construct() {
        $this->add_filter( 'wemail_admin_submenu', 'register_submenu', $this->menu_priority, 2 );
        $this->add_filter( 'wemail_get_route_data_lists', 'get_route_data_lists', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_list', 'get_route_data_list', 10, 2 );
    }

    public function register_submenu( $menu_items, $capability ) {
        $menu_items[] = [ __( 'Lists', 'wemail' ), $capability, 'admin.php?page=wemail#/lists' ];

        return $menu_items;
    }

    public function get_route_data_lists( $params, $query ) {
        return [
            'i18n' => [
                'lists' => __( 'Lists', 'wemail' )
            ],
            'lists' => $this->get_lists( $query )
        ];
    }

    public function get_route_data_list( $params, $query ) {
        return [
            'i18n' => [
                'list' => __( 'List', 'wemail' )
            ],
            'list' => $this->get_list( $params['id'] )
        ];
    }

    public function get_lists( $args ) {
        return wemail()->api->get( '/lists', $args );
    }

    public function all() {
        // wemail()->api->lists()->all()->get();
        wemail()->api->lists();

        return wemail()->api->get( '/lists/all' );
    }

    public function get_list( $id ) {
        return wemail()->api->get( "/lists/{$id}" );
    }

}
