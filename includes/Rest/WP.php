<?php

namespace WeDevs\WeMail\Rest;

use WP_Error;
use WP_Query;
use WP_REST_Controller;
use WP_REST_Server;
use WP_User_Query;

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

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/users', [
            'methods'             => WP_REST_Server::READABLE,
            'permission_callback' => [ $this, 'can_create_subscriber' ],
            'callback'            => [ $this, 'users' ],
        ] );
    }

    public function set_current_user( $request ) {
        $api_key = $request->get_header( 'X-WeMail-Key' );

        if ( ! empty( $api_key ) ) {
            $query = new WP_User_Query( [
                'fields'        => 'ID',
                'meta_key'      => 'wemail_api_key',
                'meta_value'    => $api_key
            ] );

            if ( $query->get_total() ) {
                $results = $query->get_results();
                $user_id = array_pop( $results );

                wp_set_current_user( $user_id );

                return true;
            }
        }

        return false;
    }

    public function permission( $request ) {
        return wemail()->user->can( 'update_campaign' );
    }

    public function can_create_subscriber( $request ) {
        if ( $this->set_current_user( $request ) ) {
            return wemail()->user->can( 'create_subscriber' );
        }

        return false;
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

    public function users( $request ) {
        global $wpdb;

        $current_page = $request->get_param( 'page' );

        /**
         * Filter to increase or decrease per page user count if necessary
         *
         * @since 1.0.0
         *
         * @var int
         */
        $users_per_page = apply_filters( 'wemail_import_wp_users_per_page', 100 );

        $total_users    = $wpdb->get_var('select count(*) from ' . $wpdb->users);
        $last_page      = max( (int) ceil( $total_users / $users_per_page ), 1 );

        $current_page   = absint( $current_page );

        if ( $current_page <= 0 ) {
            $current_page = 1;
        }

        if ( $current_page > $last_page ) {
            $current_page = $last_page;
        }

        $offset = ($current_page - 1) * $users_per_page;

        $users = $wpdb->get_results(
            $wpdb->prepare(
                'select ID, user_login, user_email'
                . ' from ' . $wpdb->users
                . ' order by ID asc'
                . ' limit %d, %d',
                $offset,
                $users_per_page
            ),
            ARRAY_A
        );

        $user_ids = wp_list_pluck( $users, 'ID' );

        $users_meta = $wpdb->get_results(
            $wpdb->prepare(
                'select user_id, meta_key, meta_value'
                . ' from ' . $wpdb->usermeta
                . ' where user_id in (%s) and meta_key in (%s, %s)',
                implode(', ', $user_ids),
                'first_name',
                'last_name'
            ),
            ARRAY_A
        );

        $next_page = ($current_page + 1) <= $last_page ? ($current_page + 1) : 0;

        $users = array_map( function ( &$user ) use ( $users_meta ) {
            $user['first_name'] = null;
            $user['last_name']  = null;

            foreach ( $users_meta as $meta ) {
                if (absint( $user['ID'] ) === absint( $meta['user_id'] )) {
                    $user[ $meta['meta_key'] ] = $meta['meta_value'];
                }
            }

            return $user;
        }, $users );

        $data = [
            'users'     => $users,
            'next_page' => $next_page
        ];

        return rest_ensure_response( $data );
    }

}
