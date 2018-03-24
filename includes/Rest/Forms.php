<?php

namespace WeDevs\WeMail\Rest;

use Stringy\Stringy;
use WP_Error;
use WP_REST_Response;
use WP_REST_Server;

class Forms {

    public $namespace = 'wemail/v1';

    public $rest_base = '/forms';

    public function __construct() {
        $this->register_routes();
    }

    public function register_routes() {
        register_rest_route( $this->namespace, $this->rest_base . '/(?P<id>[\w-]+)', [
            'args' => [
                'id' => [
                    'description' => __( 'Form ID', 'wemail' ),
                    'type'        => 'string',
                ],
            ],
            [
                'methods' => WP_REST_Server::CREATABLE,
                'permission_callback' => [ $this, 'permission' ],
                'callback' => [ $this, 'submit' ],
            ]
        ] );
    }

    public function permission( $request ) {
        $nonce = $request->get_header('X-WP-Nonce');

        if ( $nonce && wp_verify_nonce( $nonce, 'wp_rest' ) ) {
            return true;
        }

        return false;
    }

    public function submit( $request ) {
        $id     = $request->get_param( 'id' );
        $data   = $request->get_body_params();

        if ( empty( $data ) ) {
            return new WP_Error(
                'invalid_form_data',
                __( 'Invalid form data', 'wemail' ),
                [ 'status' => 422 ]
            );
        }

        $submitted_data = [];

        foreach ( $data as $form_input ) {
            $field_id = str_replace( 'wemail_form_field_', '', $form_input['name'] );
            $submitted_data[ $field_id ] = $form_input['value'];
        }

        wemail_set_owner_api_key();

        $response = wemail()->form->submit( $id, $submitted_data );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        return new WP_REST_Response( $response );
    }

}
