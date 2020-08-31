<?php

namespace WeDevs\WeMail\Rest\Ecommerce;

use WP_REST_Controller;
use WP_REST_Server;
use WeDevs\WeMail\Core\Ecommerce\WooCommerce\WCOrders;

class Orders extends WP_REST_Controller {

    public $namespace = 'wemail/v1';

    public $rest_base = '/ecommerce/orders';

    private $model;

    public function __construct() {
        $this->register_routes();

        $this->model = new WCOrders();
    }

    public function register_routes() {
        register_rest_route( $this->namespace, '/' . $this->rest_base, [
            [
                'methods'             => WP_REST_Server::READABLE,
                'permission_callback' => [ $this, 'permission' ],
                'callback'            => [ $this, 'orders' ],
            ]
        ] );
    }

    public function permission() {
//        return wemail()->user->can( 'view_wemail' );
        return true;
    }

    public function orders() {
        return rest_ensure_response(
            $this->model->all()
        );
    }

}
