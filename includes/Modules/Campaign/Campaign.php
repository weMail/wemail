<?php

namespace WeDevs\WeMail\Modules\Campaign;

use WeDevs\WeMail\Framework\Module;
use WeDevs\WeMail\Modules\Campaign\Event;
use WeDevs\WeMail\Modules\Campaign\Editor;

class Campaign extends Module {

    private $menu_priority = 2;

    public $event;
    public $editor;

    public function __construct() {
        $this->add_filter( 'wemail_admin_submenu', 'register_submenu', $this->menu_priority, 2 );
        $this->add_filter( 'wemail_get_route_data_campaignIndex', 'index', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_campaignCreate', 'create', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_campaignShow', 'show', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_campaignEdit', 'edit', 10, 2 );

        $this->event  = new Event();
        $this->editor = new Editor();
    }

    public function register_submenu( $menu_items, $capability ) {
        $menu_items[] = [ __( 'Campaigns', 'wemail' ), $capability, 'admin.php?page=wemail#/campaigns' ];

        return $menu_items;
    }

    public function index( $params, $query ) {
        return [
            'i18n' => [
                'campaigns' => __( 'Campaigns', 'wemail' )
            ],
            'campaigns' => wemail()->api->campaigns()->query( $query )->get()
        ];
    }

    public function create( $params, $query ) {
        return $this->editor->get_setup_data();
    }

    public function show( $params, $query ) {
        return [
            'i18n' => [
                'campaign' => __( 'List', 'wemail' )
            ],
            'campaign' => $this->get( $params['id'] )
        ];
    }

    public function edit( $params, $query ) {
        $data = $this->editor->get_setup_data();

        $data['campaign'] = $this->get( $params['id'] );

        return $data;
    }

    public function get( $id ) {
        $campaign = wemail()->api->campaigns( $id )->get();

        if ( isset( $campaign['data'] ) ) {
            $campaign = $campaign['data'];
        } else {
            $campaign = null;
        }

        return $campaign;
    }

}
