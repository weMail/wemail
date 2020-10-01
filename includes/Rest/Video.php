<?php

namespace WeDevs\WeMail\Rest;

use WP_Error;
use WP_Query;
use WP_REST_Server;
use WP_REST_Controller;

class Video extends WP_REST_Controller {

    public $namespace = 'wemail/v1';

    public $rest_base = '/video-thumb';

    public function __construct() {
        $this->register_routes();
    }

    public function register_routes() {
        register_rest_route(
            $this->namespace,
            '/' . $this->rest_base,
            [
                'methods'             => WP_REST_Server::READABLE,
                'permission_callback' => [ $this, 'permission' ],
                'callback'            => [ $this, 'thumb' ],
            ]
        );
    }

    public function permission( $request ) {
        return wemail()->user->can( 'update_campaign' );
    }

    public function thumb( $request ) {
        $source = $request->get_param( 'source' );
        $video_id = $request->get_param( 'video_id' );

        if ( ! $source || ! $video_id ) {
            return new WP_Error(
                'invalid_request',
                __( 'source and video_id fields are required', 'wemail' ),
                [ 'status' => 400 ]
            );
        }

        $thumbnail = $this->get_existing_thumbnail( $source, $video_id );

        if ( ! is_null( $thumbnail ) ) {
            $image_link = wp_get_attachment_url( $thumbnail->ID );
        } else {
            $image_link = $this->get_preview_image( $source, $video_id );

            if ( is_wp_error( $image_link ) ) {
                return $image_link;
            }
        }

        return rest_ensure_response( [ 'image' => $image_link ] );
    }

    /**
     * Get existing video thumbnail
     *
     * @param $source
     * @param $video_id
     *
     * @return object|null
     */
    protected function get_existing_thumbnail( $source, $video_id ) {
        $query = new WP_Query(
            [
                'post_type' => 'attachment',
                'post_status' => 'inherit',
                'posts_per_page' => 1,
                'meta_query' => [
                    [
                        'key'   => sprintf( 'wemail_%s_id', $source ),
                        'value' => $video_id,
                    ],
                ],
            ]
        );

        if ( ! $query->have_posts() ) {
            return null;
        }

        return $query->posts[0];
    }

    /**
     * Get preview image from weMail API
     *
     * @param $source
     * @param $video_id
     *
     * @return false|string|WP_Error
     */
    protected function get_preview_image( $source, $video_id ) {
        wemail_set_owner_api_key();
        $response = wemail()->api->get_response( sprintf( '/thumbnails/%s/%s', $source, $video_id ) );

        if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
            $body = wp_remote_retrieve_body( $response );
            $body = json_decode( $body );
            return new WP_Error(
                'invalid_request',
                $body->message,
                [ 'status' => wp_remote_retrieve_response_code( $response ) ]
            );
        }
        $content_type = wp_remote_retrieve_header( $response, 'content-type' );
        $filename = sprintf( '%s.%s', $video_id, explode( '/', $content_type )[1] );
        $image = wp_remote_retrieve_body( $response );

        $upload_file = wp_upload_bits( $filename, null, $image );

        $wp_filetype = wp_check_filetype( $filename, null );
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_parent' => 0,
            'post_title' => $video_id,
            'post_content' => '',
            'post_status' => 'inherit',
        );

        $attachment_id = wp_insert_attachment( $attachment, $upload_file['file'] );

        require_once ABSPATH . 'wp-admin/includes/image.php';

        $attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file['file'] );

        wp_update_attachment_metadata( $attachment_id, $attachment_data );

        update_post_meta( $attachment_id, sprintf( 'wemail_%s_id', $source ), $video_id );

        return wp_get_attachment_url( $attachment_id );
    }
}
