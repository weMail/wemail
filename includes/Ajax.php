<?php
namespace WeDevs\WeMail;

use WeDevs\WeMail\Traits\Ajax as AjaxTrait;

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
        $this->add_ajax_action( 'get_customizer_data' );
    }

    /**
     * Get a named Vue.js route data
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function get_route_data() {
        $this->verify_nonce();

        if ( empty( $_GET['name'] ) ) {
            $this->send_error( [ 'msg' => __( 'Route name is required', 'wemail' ) ] );
        }

        $name = $_GET['name'];
        $params = ! empty( $_GET['params'] ) ? $_GET['params'] : [];
        $query  = ! empty( $_GET['query'] ) ? $_GET['query'] : [];

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
         * @param array $_GET   HTTP $_GET data
         */
        $data = apply_filters( "wemail_get_route_data_{$name}", $params, $query, $_GET );

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

    public function get_customizer_data() {
        $this->verify_nonce();

        // check context then return data. using hardcoded for now

        $customizer = wemail()->campaign->editor->get_customizer_data();

        $this->send_success( [
            'customizer' => $customizer
        ] );
    }

}

