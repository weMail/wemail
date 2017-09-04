<?php
namespace WeDevs\WeMail;

use WeDevs\WeMail\Framework\Traits\Ajax as AjaxTrait;

class Ajax {

    use AjaxTrait;

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->add_ajax_action( 'get_route_data' );
        $this->add_ajax_action( 'get_country_states' );
    }

    /**
     * Get Vue.js route data
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function get_route_data() {
        $this->verify_nonce();

        if ( empty( $_GET['routes'] ) ) {
            $this->send_error( [ 'msg' => __( 'Route name is required', 'wemail' ) ] );
        }

        $routes = $_GET['routes'];
        $params = ! empty( $_GET['params'] ) ? $_GET['params'] : [];
        $query  = ! empty( $_GET['query'] ) ? $_GET['query'] : [];

        $data = [];

        foreach ( $_GET['routes'] as $route_name ) {
            /**
             * Get Vue.js route data for a named route
             *
             * Use add_filter to this filter to provide route data
             * for a named route.
             *
             * @since 1.0.0
             *
             * @param array $params Vue.js $route.params
             * @param array $query  Vue.js $route.query
             * @param array $route  Route names that requires data to render
             * @param array $_GET   HTTP $_GET data
             */
            $data[ $route_name ] = apply_filters( 'wemail-get-route-data-' . $route_name, $params, $query, $routes, $_GET );
        }

        $this->send_success( $data );
    }

    /**
     * Get states/province/divisions for a country
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function get_country_states() {
        $this->verify_nonce();

        if ( empty( $_GET['country'] ) ) {
            $states = [];
        } else {
            $states = wemail_get_country_states( $_GET['country'] );
        }

        $this->send_success(['states' => $states]);
    }

}

