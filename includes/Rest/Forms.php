<?php

namespace WeDevs\WeMail\Rest;

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
        register_rest_route(
            $this->namespace,
            $this->rest_base,
            array(
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'permission_callback' => array( $this, 'permission' ),
                    'callback'            => array( $this, 'store' ),
                ),
            )
        );

        register_rest_route(
            $this->namespace,
            $this->rest_base,
            array(
                array(
                    'methods'             => WP_REST_Server::DELETABLE,
                    'permission_callback' => array( $this, 'permission' ),
                    'callback'            => array( $this, 'destroy' ),
                ),
            )
        );

        register_rest_route(
            $this->namespace,
            $this->rest_base . '/restore',
            array(
                array(
                    'methods'             => WP_REST_Server::EDITABLE,
                    'permission_callback' => array( $this, 'permission' ),
                    'callback'            => array( $this, 'restore' ),
                ),
            )
        );

        register_rest_route(
            $this->namespace,
            $this->rest_base . '/sync',
            array(
                array(
                    'methods'             => WP_REST_Server::EDITABLE,
                    'permission_callback' => array( $this, 'permission' ),
                    'callback'            => array( $this, 'sync' ),
                ),
            )
        );

        register_rest_route(
            $this->namespace,
            $this->rest_base . '/(?P<id>[\w-]+)/update',
            array(
                'args' => array(
                    'id' => array(
                        'description' => __( 'Form ID', 'wemail' ),
                        'type'        => 'string',
                        'required'    => true,
                    ),
                ),
                array(
                    'methods'             => WP_REST_Server::EDITABLE,
                    'permission_callback' => array( $this, 'permission' ),
                    'callback'            => array( $this, 'update' ),
                ),
            )
        );

        register_rest_route(
            $this->namespace,
            $this->rest_base . '/(?P<id>[\w-]+)',
            array(
                'args' => array(
                    'id' => array(
                        'description' => __( 'Form ID', 'wemail' ),
                        'type'        => 'string',
                    ),
                ),
                array(
                    'methods'             => WP_REST_Server::CREATABLE,
                    'permission_callback' => array( $this, 'permission' ),
                    'callback'            => array( $this, 'submit' ),
                ),
            )
        );

        register_rest_route(
            $this->namespace,
            $this->rest_base . '/(?P<id>[\w-]+)/visitors',
            array(
                'args' => array(
                    'id' => array(
                        'description' => __( 'Form ID', 'wemail' ),
                        'type'        => 'string',
                    ),
                ),
                array(
                    'methods'             => WP_REST_Server::EDITABLE,
                    'permission_callback' => array( $this, 'permission' ),
                    'callback'            => array( $this, 'increment_visitor' ),
                ),
            )
        );
    }

    public function permission( $request ) {
        $nonce = $request->get_header( 'X-WP-Nonce' );

        if ( $nonce && wp_verify_nonce( $nonce, 'wp_rest' ) ) {
            return true;
        }

        return false;
    }

    public function submit( $request ) {
        $id   = $request->get_param( 'id' );
        $data = $request->get_body_params();

        if ( empty( $data ) ) {
            return new WP_Error(
                'invalid_form_data',
                __( 'Invalid form data', 'wemail' ),
                array( 'status' => 422 )
            );
        }

        $submitted_data = array();

        foreach ( $data as $form_input ) {
            $field_id                    = str_replace( 'wemail_form_field_', '', $form_input['name'] );
            $submitted_data[ $field_id ] = $form_input['value'];
        }

        wemail_set_owner_api_key();

        $response = wemail()->form->submit( $id, $submitted_data );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        return new WP_REST_Response( $response );
    }

    public function increment_visitor( $request ) {
        $form_id = $request->get_param( 'id' );

        wemail_set_owner_api_key();

        $response = wemail()->form->increment_visitor_count( $form_id );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        return new WP_REST_Response( $response );
    }

    /**
     * Create a new form
     *
     * @param $request \WP_REST_Request
     *
     * @return WP_REST_Response
     */
    public function store( $request ) {
        $data = $request->get_json_params();
        if ( empty( $data ) ) {
            $data = $request->get_body_params();
        }

        wemail()->form->create( $data );

        return new WP_REST_Response( array( 'success' => true ), 201 );
    }

    /**
     * Update form data
     *
     * @param $request \WP_REST_Request
     *
     * @return WP_REST_Response
     */
    public function update( $request ) {
        $data = $request->get_json_params();
        if ( empty( $data ) ) {
            $data = $request->get_body_params();
        }
        $id = $request->get_param( 'id' );

        wemail()->form->update( $data, $id );

        return new WP_REST_Response( array( 'success' => true ) );
    }

    /**
     * Delete Form
     *
     * @param $request \WP_REST_Request
     *
     * @return WP_REST_Response
     */
    public function destroy( $request ) {
        $ids         = $request->get_param( 'ids' );
        $soft_delete = $request->get_param( 'soft_delete' );

        wemail()->form->delete( $ids, wemail_validate_boolean( $soft_delete ) );

        return new WP_REST_Response( array( 'success' => true ) );
    }

    /**
     * Restore forms
     *
     * @param $request \WP_REST_Request
     *
     * @return WP_REST_Response
     */
    public function restore( $request ) {
        $ids = $request->get_param( 'ids' );

        wemail()->form->update( array( 'deleted_at' => null ), $ids );

        return new WP_REST_Response( array( 'success' => true ) );
    }

    /**
     * Sync forms
     *
     * @param $request
     *
     * @return WP_REST_Response
     */
    public function sync( $request ) {
        wemail()->form->sync();

        return new WP_REST_Response( array( 'success' => true ) );
    }
}
