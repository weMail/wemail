<?php

namespace WeDevs\WeMail\Modules\Lists;

use WeDevs\WeMail\Framework\Module;

class Lists extends Module {

    /**
     * Submenu priority
     *
     * @since 1.0.0
     *
     * @var integer
     */
    public $menu_priority = 80;

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->add_filter( 'wemail_admin_submenu', 'register_submenu', $this->menu_priority, 2 );
        $this->add_filter( 'wemail_get_route_data_listsIndex', 'index', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_listsCreate', 'create', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_listsEdit', 'edit', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_listsSubscribers', 'subscribers', 10, 2 );
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
        if ( wemail()->user->can( 'view_list' ) ) {
            $menu_items[] = [ __( 'Lists', 'wemail' ), $capability, 'admin.php?page=wemail#/lists' ];
        }

        return $menu_items;
    }

    /**
     * listsIndex route data
     *
     * @since 1.0.0
     *
     * @param array $params
     * @param array $query
     *
     * @return array
     */
    public function index( $params, $query ) {
        return [
            'lists'     => $this->all( $query ),
            'listTable' => [
                'columns' => [
                    'name',
                    'description',
                    'subscribed',
                    'unsubscribed',
                    'unconfirmed',
                    'createdAt'
                ],
                'sortableColumns' => [
                    'name'          => 'name',
                    'subscribed'    => 'subscribed',
                    'unsubscribed'  => 'unsubscribed',
                    'unconfirmed'   => 'unconfirmed',
                    'createdAt'     => 'created_at'
                ]
            ]
        ];
    }

    public function create() {
        return [
            'list' => [
                'name'        => '',
                'description' => '',
                'type'        => 'public'
            ]
        ];
    }

    public function edit( $params ) {
        return [
            'list' => $this->get( $params['id'] )
        ];
    }

    /**
     * listsShow route data
     *
     * @since 1.0.0
     *
     * @param array $params
     * @param array $query
     *
     * @return array
     */
    public function subscribers( $params, $query ) {
        return [
            'i18n' => [
            ],
            'list' => $this->get( $params['id'] )
        ];
    }

    /**
     * Get a list of lists
     *
     * @since 1.0.0
     *
     * @param array $query
     *
     * @return array
     */
    public function all( $query = [] ) {
        return wemail()->api->lists()->query( $query )->get();
    }

    /**
     * Get all lists
     *
     * id-name paired items
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function items() {
        return wemail()->api->lists()->items()->get();
    }

    /**
     * Get a single list
     *
     * @since 1.0.0
     *
     * @param integer $id
     *
     * @return array
     */
    public function get( $id ) {
        return wemail()->api->lists( $id )->get();
    }

}
