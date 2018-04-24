<?php

namespace WeDevs\WeMail\Rest;

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
            'permission_callback' => [ $this, 'can_update_campaign' ],
            'callback'            => [ $this, 'post_types' ],
        ] );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/posts', [
            'methods'             => WP_REST_Server::READABLE,
            'permission_callback' => [ $this, 'can_update_campaign' ],
            'callback'            => [ $this, 'posts' ],
        ] );

        register_rest_route( $this->namespace, '/' . $this->rest_base . '/user-roles', [
            'methods'             => WP_REST_Server::READABLE,
            'permission_callback' => [ $this, 'can_manage_settings' ],
            'callback'            => [ $this, 'user_roles' ],
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

    public function can_update_campaign( $request ) {
        return wemail()->user->can( 'update_campaign' );
    }

    public function can_manage_settings( $request ) {
        return wemail()->user->can( 'manage_settings' );
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

    public function user_roles( $request ) {
        global $wp_roles;

        $user_roles = [];

        $roles = $wp_roles->get_names();

        foreach ($roles as $name => $title) {
            array_push($user_roles, [
                'name' => $name,
                'title' => $title
            ]);
        }

        return rest_ensure_response( [ 'data' => $user_roles ] );
    }

    public function users( $request ) {
        global $wpdb;

        $roles        = $request->get_param( 'roles' );
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

        $args = [
            'role__in'  => ! empty( $roles ) ? $roles : [],
            'number'    => $users_per_page,
            'offset'    => $offset,
            'fields'    => 'all_with_meta'
        ];

        $wp_users = get_users( $args );

        $users = [];

        foreach ($wp_users as $user) {
            $users[] = [
                'ID' => $user->ID,
                'first_name'    => $user->get('first_name'),
                'last_name'     => $user->get('last_name'),
                'user_email'    => $user->user_email,
                'user_login'    => $user->user_login
            ];
        }

        $next_page = ($current_page + 1) <= $last_page ? ($current_page + 1) : 0;

        $data = [
            'users'     => $users,
            'next_page' => $next_page
        ];

        return rest_ensure_response( $data );
    }

}
