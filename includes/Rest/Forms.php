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

        $form = wemail()->form->get( $id );

        if ( empty( $form['template']['fields'] ) ) {
            return null;
        }

        $form_fields = $form['template']['fields'];

        $email      = '';
        $first_name = '';
        $last_name  = '';

        foreach ( $form_fields as $form_field ) {
            $field_id = $form_field['id'];
            $type = $form_field['type'];
            $is_required = isset( $form_field['required'] ) && wemail_validate_boolean( $form_field['required'] );

            if ( $is_required && ! array_key_exists( $field_id , $submitted_data ) )  {
                return new WP_Error(
                    'missing_required_field',
                    sprintf( __( '% field is required', 'wemail' ), $form_field['label'] ),
                    [ 'status' => 422 ]
                );
            }

            // Should be throw error by previous condition. But in case we have a messed-up
            // settings that doesn't contain email field settings
            if ( $type === 'email' && ! array_key_exists( $field_id , $submitted_data ) ) {
                return new WP_Error(
                    'missing_email_field',
                    sprintf( __( 'Email field is required', 'wemail' ), $form_field['label'] ),
                    [ 'status' => 422 ]
                );
            }

            switch ( $type ) {
                case 'email':
                    $email = $submitted_data[ $form_field['id'] ];
                    break;

                case 'fullName':
                    $full_name = $submitted_data[ $form_field['id'] ];
                    $full_name = explode( ' ', $full_name );

                    if ( count( $full_name ) > 1 ) {
                        $last_name = trim( array_pop( $full_name ) );
                    }

                    $first_name = trim( implode( ' ', $full_name ) );
                    break;

                case 'firstName':
                    $first_name = $submitted_data[ $form_field['id'] ];
                    break;

                case 'lastName':
                    $last_name = $submitted_data[ $form_field['id'] ];
                    break;
            }
        }

        $list_id = $form['settings']['actions'][0]['value'][0];

        $subscriber = wemail()->subscriber->get( $email );

        if ( $subscriber ) {
            $subscriber = wemail()->subscriber->subscribe_to_lists( $subscriber['id'], [ $list_id ] );

            $status_code = 200;

        } else {
            $subscriber = wemail()->subscriber->create( [
                'email'         => $email,
                'first_name'    => $first_name,
                'last_name'     => $last_name,
                'lists'         => [ $list_id ]
            ] );

            $status_code = 201;
        }

        if ( ! $subscriber ) {
            return new WP_Error(
                'something_went_wrong',
                __( 'Something went wrong', 'wemail' ),
                [ 'status' => 422 ]
            );
        }

        $data = [
            'success' => true,
            'on_submit' => $form['settings']['onSubmit'],
            'message' => $form['settings']['message'],
            'redirect_to' => $form['settings']['redirectTo'],
        ];

        return new WP_REST_Response( $data, $status_code );
    }

}
