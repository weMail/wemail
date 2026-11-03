<?php

namespace WeDevs\WeMail\Rest;

use League\Csv\Reader;
use WeDevs\WeMail\RestController;

class Csv extends RestController {

    public $rest_base = '/csv/(?P<id>[\d]+)';

    public function register_routes() {
        $this->get( '', 'csv_file_info', 'can_csv_upload' );
        $this->get( '/meta-fields', 'meta_fields', 'can_meta_fields' );
        $this->get( '/subscribers', 'subscribers', 'can_get_subscribers' );
    }

    /**
     * Read a CSV file attachment
     *
     * @since 2.1.7 Only reads existing CSV attachments.
     *
     * @param int|string $file_id
     *
     * @return \League\Csv\Reader|\WP_Error
     */
    private function reader( $file_id ) {
        $post = get_post( absint( $file_id ) );

        if (
            ! $post
            || 'attachment' !== $post->post_type
            || ! in_array( get_post_mime_type( $post ), array( 'text/csv', 'text/plain' ), true )
        ) {
            return $this->respond_error( __( 'Invalid CSV file.', 'wemail' ), 'invalid_csv_file', self::HTTP_UNPROCESSABLE_ENTITY );
        }

        $file_url    = wp_get_attachment_url( $post->ID );
        $response    = wp_remote_get( $file_url );
        $csv_content = wp_remote_retrieve_body( $response );

        return Reader::createFromString( $csv_content );
    }

    public function csv_file_info( $request ) {
        $file_id = $request->get_param( 'id' );

        $reader = $this->reader( $file_id );

        if ( is_wp_error( $reader ) ) {
            return $reader;
        }

        $count = iterator_count( $reader );

        $data = array(
            'total' => $count - 1, // Subtract 1 for header row
        );

        return $this->respond( $data );
    }

    public function meta_fields( $request ) {
        $file_id = $request->get_param( 'id' );
        $reader = $this->reader( $file_id );

        if ( is_wp_error( $reader ) ) {
            return $reader;
        }

        $reader->setHeaderOffset( 0 );
        $meta_fields = array_keys( $reader->fetchOne() );

        return $this->respond(
            array(
                'fields' => $meta_fields,
            )
        );
    }

    public function subscribers( $request ) {
        $file_id    = $request->get_param( 'id' );
        $limit      = $request->get_param( 'limit' );
        $offset     = $request->get_param( 'offset' );

        $limit = $limit ? $limit : 500;
        $offset = $offset ? $offset : 0;

        $reader = $this->reader( $file_id );

        if ( is_wp_error( $reader ) ) {
            return $reader;
        }

        // Set the header offset so records are returned as associative arrays
        $reader->setHeaderOffset( 0 );

        // Use Statement for offset/limit
        $stmt = \League\Csv\Statement::create()
            ->offset( $offset )
            ->limit( $limit );

        $records = $stmt->process( $reader ); // Iterator of associative arrays
        $subscribers = iterator_to_array( $records );

        return $this->respond(
            array(
                'subscribers' => $subscribers,
            )
        );
    }
}
