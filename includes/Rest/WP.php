<?php

namespace WeDevs\WeMail\Rest;

use WP_Error;
use WP_Query;
use WP_REST_Controller;
use WP_REST_Server;

class WP extends WP_REST_Controller {

    public $namespace = 'wemail/v1';

    public $rest_base = '/wp';

    public function __construct() {
        $this->register_routes();
    }

    public function register_routes() {
        register_rest_route( $this->namespace, '/' . $this->rest_base . '/post-types', [
            'methods'             => WP_REST_Server::READABLE,
            'permission_callback' => [ $this, 'permission' ],
            'callback'            => [ $this, 'post_types' ],
        ] );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/posts', [
            'methods'             => WP_REST_Server::READABLE,
            'permission_callback' => [ $this, 'permission' ],
            'callback'            => [ $this, 'posts' ],
        ] );
    }

    public function permission( $request ) {
        return wemail()->user->can( 'update_campaign' );
    }

    public function post_types( $request ) {
        $post_types = [];
        $registered_types = get_post_types( [ 'public' => true ], 'objects' );

        foreach ( $registered_types as $name => $object ) {
            if ( $object->name === 'attachment' ) {
                continue;
            }

            $post_types[] = [
                'name' => $object->name,
                'label' => $object->label
            ];
        }

        return rest_ensure_response( $post_types );
    }

    public function posts( $request ) {
        $post_type = $request->get_param( 'post_type' );
        $search = $request->get_param( 's' );

        $posts = [];

        $args = [
            'post_type' => $post_type ? $post_type : 'post',
            's' => $search
        ];

        // The Query
        $query = new WP_Query( $args );

        // The Loop
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();

                $id = $query->post->ID;

                $post_thumb_id = get_post_thumbnail_id( $id );
                $image = wemail_get_image_url( $post_thumb_id );

                $posts [] = [
                    'id' => $id,
                    'image' => $image,
                    'title' => strip_tags( $query->post->post_title ),
                    'postType' => $query->post->post_type,
                    'postStatus' => $query->post->post_status,
                    'url' => get_permalink( $id ),
                    'content' => strip_tags( $query->post->post_content ),
                    'excerpt' => strip_tags( $query->post->post_excerpt )
                ];
            }

            wp_reset_postdata();
        }

        return rest_ensure_response( $posts );
    }

}
