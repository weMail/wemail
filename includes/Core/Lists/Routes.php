<?php

namespace WeDevs\WeMail\Core\Lists;

use WeDevs\WeMail\Traits\Hooker;

class Routes {

    use Hooker;

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->add_filter( 'wemail_get_route_data_listsIndex', 'index', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_listsCreate', 'create', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_listsEdit', 'edit', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_listsSubscribers', 'subscribers', 10, 2 );
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
            'lists'     => wemail()->lists->all( $query ),
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

    /**
     * listsCreate route data
     *
     * @since 1.0.0
     *
     * @param array $params
     * @param array $query
     *
     * @return array
     */
    public function create( $params, $query ) {
        return [
            'list' => [
                'name'        => '',
                'description' => '',
                'type'        => 'public'
            ]
        ];
    }

    /**
     * listsEdit route data
     *
     * @since 1.0.0
     *
     * @param array $params
     * @param array $query
     *
     * @return array
     */
    public function edit( $params, $query ) {
        return [
            'list' => wemail()->lists->get( $params['id'] )
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

}
