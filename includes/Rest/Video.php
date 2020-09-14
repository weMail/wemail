<?php

namespace WeDevs\WeMail\Rest;

use WP_Error;
use WP_REST_Controller;
use WP_REST_Server;

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

        $image_link = '';

        if ( $source === 'youtube' ) {
            $response = wp_remote_head( "http://img.youtube.com/vi/$video_id/maxresdefault.jpg" );
            $code     = wp_remote_retrieve_response_code( $response );

            if ( $code !== 200 ) {
                $response = wp_remote_head( "http://img.youtube.com/vi/$video_id/0.jpg" );
                $code     = wp_remote_retrieve_response_code( $response );
                if ( $code === 200 ) {
                    $image_link = "http://img.youtube.com/vi/$video_id/0.jpg";
                }
            } else {
                $image_link = "http://img.youtube.com/vi/$video_id/maxresdefault.jpg";
            }
        } elseif ( $source === 'vimeo' ) {
            $response = wp_remote_get( "https://vimeo.com/api/v2/video/$video_id.xml" );
            $code     = wp_remote_retrieve_response_code( $response );
            if ( $code === 200 ) {
                $body = wp_remote_retrieve_body( $response );
                $xml  = simplexml_load_string( $body );
                $json = wp_json_encode( $xml );
                $obj  = wp_json_encode( $json );

                if ( ! empty( $obj->video->thumbnail_large ) ) {
                    $image_link = $obj->video->thumbnail_large;
                } else {
                    $image_link = $obj->video->thumbnail_medium;
                }
            }
        }

        return rest_ensure_response( [ 'image' => $image_link ] );
    }

}
