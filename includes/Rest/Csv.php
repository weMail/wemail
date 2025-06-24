<?php

namespace WeDevs\WeMail\Rest;

use League\Csv\Reader;
use WP_REST_Response;
use WP_REST_Server;
use WP_User_Query;

class Csv {

    public $namespace = 'wemail/v1';

    public $rest_base = '/csv/(?P<id>[\d]+)';

    public function __construct() {
        $this->register_routes();
    }

    public function register_routes() {
        register_rest_route(
            $this->namespace,
            $this->rest_base,
            array(
                'args' => array(
                    'id' => array(
                        'description' => __( 'CSV file attachment id', 'wemail' ),
                        'type'        => 'integer',
                    ),
                ),
                array(
                    'methods' => WP_REST_Server::READABLE,
                    'permission_callback' => array( $this, 'permission' ),
                    'callback' => array( $this, 'csv_file_info' ),
                ),
            )
        );

        register_rest_route(
            $this->namespace,
            $this->rest_base . '/meta-fields',
            array(
                'args' => array(
                    'id' => array(
                        'description' => __( 'CSV file attachment id', 'wemail' ),
                        'type'        => 'integer',
                    ),
                ),
                array(
                    'methods' => WP_REST_Server::READABLE,
                    'permission_callback' => array( $this, 'permission' ),
                    'callback' => array( $this, 'meta_fields' ),
                ),
            )
        );

        register_rest_route(
            $this->namespace,
            $this->rest_base . '/subscribers',
            array(
                'args' => array(
                    'id' => array(
                        'description' => __( 'CSV file attachment id', 'wemail' ),
                        'type'        => 'integer',
                    ),
                ),
                array(
                    'methods' => WP_REST_Server::READABLE,
                    'permission_callback' => array( $this, 'permission' ),
                    'callback' => array( $this, 'subscribers' ),
                ),
            )
        );
    }

    public function permission( $request ) {
        $api_key = $request->get_header( 'X-WeMail-Key' );

        if ( ! empty( $api_key ) ) {
            $query = new WP_User_Query(
                array(
                    'fields'        => 'ID',
                    'meta_key'      => 'wemail_api_key',
                    'meta_value'    => $api_key,
                )
            );

            if ( $query->get_total() ) {
                $results = $query->get_results();
                $user_id = array_pop( $results );

                wp_set_current_user( $user_id );

                return wemail()->user->can( 'create_subscriber' );
            }
        }

        return false;
    }

    private function reader( $file_id ) {
        $file_path = get_attached_file( $file_id, false );

        return Reader::createFromPath( $file_path, 'r' );
    }

    public function csv_file_info( $request ) {
        $file_id = $request->get_param( 'id' );

        $reader = $this->reader( $file_id );

        $count = 0;
        foreach ( $reader as $record ) {
            ++$count;
        }

        $data = array(
            'total' => $count - 1, // Subtract 1 for header row
        );

        return new WP_REST_Response( $data, 200 );
    }

    public function meta_fields( $request ) {
        $file_id = $request->get_param( 'id' );

        $file_path = get_attached_file( $file_id, false );

        $reader = Reader::createFromPath( $file_path, 'r' );

        $meta_fields = $reader->fetchOne();

        $data = array(
            'fields' => $meta_fields,
        );

        return new WP_REST_Response( $data, 200 );
    }

    public function subscribers( $request ) {
        $file_id    = $request->get_param( 'id' );
        $limit      = $request->get_param( 'limit' );
        $offset     = $request->get_param( 'offset' );

        $limit = $limit ? $limit : 500;
        $offset = $offset ? $offset : 0;

        $reader = $this->reader( $file_id );

        // Set the header offset so records are returned as associative arrays
        $reader->setHeaderOffset( 0 );

        // Use Statement for offset/limit
        $stmt = \League\Csv\Statement::create()
            ->offset( $offset )
            ->limit( $limit );

        $records = $stmt->process( $reader ); // Iterator of associative arrays
        $subscribers = iterator_to_array( $records );

        $data = array(
            'subscribers' => $subscribers,
        );

        return new WP_REST_Response( $data, 200 );
    }
}
