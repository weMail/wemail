<?php

namespace WeDevs\WeMail\Rest\Ecommerce;

use WP_REST_Controller;
use WP_REST_Server;
use WeDevs\WeMail\Core\Ecommerce\WooCommerce\WCProducts;

class Products extends WP_REST_Controller {

    public $namespace = 'wemail/v1';

    public $rest_base = '/ecommerce/products';

    private $model;

    public function __construct() {
        $this->register_routes();

        $this->model = new WCProducts();
    }

    public function register_routes() {
        register_rest_route( $this->namespace, '/' . $this->rest_base, [
            [
                'methods'             => WP_REST_Server::READABLE,
                'permission_callback' => [ $this, 'permission' ],
                'callback'            => [ $this, 'products' ],
            ]
        ] );
    }

    public function permission() {
        return wemail()->user->can( 'view_wemail' );
    }

    public function products() {
        return rest_ensure_response(
            $this->model->all()
        );
    }

}
