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
    public $menu_priority = 90;

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
        $menu_items[] = [ __( 'Lists', 'wemail' ), $capability, 'admin.php?page=wemail#/lists' ];

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
            'i18n'      => [
                'lists'              => __( 'Lists', 'wemail' ),
                'addNew'             => __( 'Add New', 'wemail' ),
                'bulkActions'        => __( 'Bulk Actions', 'wemail' ),
                'apply'              => __( 'Apply', 'wemail' ),
                'items'              => __( 'items', 'wemail' ),
                'delete'             => __( 'Delete', 'wemail' ),
                'edit'               => __( 'Edit', 'wemail' ),
                'viewSubscribers'    => __( 'View Subscribers', 'wemail' ),
                'name'               => __( 'Name', 'wemail' ),
                'description'        => __( 'Description', 'wemail' ),
                'subscribed'         => __( 'Subscribed', 'wemail' ),
                'unsubscribed'       => __( 'Unsubscribed', 'wemail' ),
                'unconfirmed'        => __( 'Unconfirmed', 'wemail' ),
                'createdAt'          => __( 'Created At', 'wemail' ),
                'searchLists'        => __( 'Search Lists', 'wemail' ),
                'noListFound'        => __( 'no list found', 'wemail' ),
                'all'                => __( 'All', 'wemail' ),
                'public'             => __( 'Public', 'wemail' ),
                'private'            => __( 'Private', 'wemail' ),
                'cancel'             => __( 'Cancel', 'wemail' ),
                'deleteListsWarnMsg' => __( 'Are you sure you want to delete these lists?', 'wemail' ),
                'deleteListWarnMsg'  => __( 'Are you sure you want to delete this list?', 'wemail' )
            ],
            'lists'     => wemail()->lists->all( $query ),
            'listTable' => [
                'columns'   => [
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

    private function modal_i18n() {
        return [
            'addNewList'        => __( 'Add New List', 'wemail' ),
            'name'              => __( 'Name', 'wemail' ),
            'description'       => __( 'Description', 'wemail' ),
            'makeItPrivate'     => __( 'Make it private', 'wemail' ),
            'requiredField'     => __( 'Required field', 'wemail' ),
            'cancel'            => __( 'Cancel', 'wemail' ),
            'save'              => __( 'Save', 'wemail' ),
            'whatIsPrivateMsg'  => __( "Subscribers cannot unsubscribe from a private list. Also, they don't need to double opt-in for a private list.", 'wemail' )
        ];
    }

    public function create() {
        return [
            'i18n' => $this->modal_i18n(),
            'list' => [
                'name'        => '',
                'description' => '',
                'type'        => 'public'
            ]
        ];
    }

    public function edit( $params ) {
        return [
            'i18n' => $this->modal_i18n(),
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
            'list' => wemail()->lists->get( $params['id'] )
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
        $query = [
            'per_page' => -1,
            'fields' => 'id, name',
            'orderby' => 'name',
            'order' => 'asc'
        ];

        return wemail()->api->lists()->query( $query )->get();
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
