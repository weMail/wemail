<?php

namespace WeDevs\WeMail\Rest;

use League\Csv\Exception as CsvException;
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
     * The file is read from this server's disk when it is there. Fetching it
     * from its own URL fails on a site that can't reach itself over HTTP (a
     * blocked loopback request, HTTP authentication, a certificate WordPress
     * doesn't trust) and downloads the whole file again for every request.
     * The URL is used only for a file kept elsewhere, such as offloaded media.
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

        $path = get_attached_file( $post->ID );

        if ( $path && is_readable( $path ) ) {
            if ( 0 === filesize( $path ) ) {
                return $this->empty_file_error();
            }

            return Reader::createFromPath( $path, 'r' );
        }

        $response = wp_remote_get( wp_get_attachment_url( $post->ID ) );

        if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
            return $this->respond_error( __( 'The CSV file could not be read. Upload it again.', 'wemail' ), 'csv_file_unreadable', self::HTTP_UNPROCESSABLE_ENTITY );
        }

        $csv_content = wp_remote_retrieve_body( $response );

        if ( '' === trim( $csv_content ) ) {
            return $this->empty_file_error();
        }

        return Reader::createFromString( $csv_content );
    }

    /**
     * The error for a CSV file with nothing in it
     *
     * @return \WP_Error
     */
    private function empty_file_error() {
        return $this->respond_error( __( 'The CSV file is empty.', 'wemail' ), 'empty_csv_file', self::HTTP_UNPROCESSABLE_ENTITY );
    }

    /**
     * The error for a CSV file the reader can't parse
     *
     * Its first row must hold the column names, no two of them the same: a
     * file with no first row, or with a repeated name, can't be mapped.
     *
     * @return \WP_Error
     */
    private function invalid_header_error() {
        return $this->respond_error(
            __( 'The CSV file could not be read. Its first row must hold the column names, each one different.', 'wemail' ),
            'invalid_csv_header',
            self::HTTP_UNPROCESSABLE_ENTITY
        );
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

        try {
            $reader->setHeaderOffset( 0 );
            $meta_fields = array_keys( $reader->fetchOne() );
        } catch ( CsvException $e ) {
            return $this->invalid_header_error();
        }

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

        try {
            // Set the header offset so records are returned as associative arrays
            $reader->setHeaderOffset( 0 );

            // Use Statement for offset/limit
            $stmt = \League\Csv\Statement::create()
                ->offset( $offset )
                ->limit( $limit );

            $records = $stmt->process( $reader ); // Iterator of associative arrays
            $subscribers = iterator_to_array( $records );
        } catch ( CsvException $e ) {
            return $this->invalid_header_error();
        }

        return $this->respond(
            array(
                'subscribers' => $subscribers,
            )
        );
    }
}
