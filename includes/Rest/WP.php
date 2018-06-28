<?php

namespace WeDevs\WeMail\Rest;

use WP_Query;
use WeDevs\WeMail\RestController;

class WP extends RestController {

    /**
     * Route base url
     *
     * @since 1.0.0
     *
     * @var string
     */
    protected $rest_base = '/wp';

    /**
     * Query for users after this ID
     *
     * @since 1.0.0
     *
     * @var int
     */
    private $user_query_after_id = 0;

    /**
     * Register routes
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function register_routes() {
        $this->get('/post-types', 'post_types', 'can_update_campaign');
        $this->get('/posts', 'posts', 'can_update_campaign');
        $this->get('/user-roles', 'user_roles', 'can_manage_settings');
        $this->get('/users', 'users', 'can_create_subscriber');
    }

    /**
     * Get all public post types
     *
     * @since 1.0.0
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|mixed
     */
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

    /**
     * Get public posts
     *
     * @since 1.0.0
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|mixed
     */
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

    /**
     * Get all user roles
     *
     * @since 1.0.0
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|mixed
     */
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

    /**
     * Get WordPress Users
     *
     * @since 1.0.0
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|mixed
     */
    public function users( $request ) {
        $roles   = $request->get_param( 'roles' );
        $include = $request->get_param( 'include' );

        $args = [
            'role__in'    => ! empty( $roles ) ? $roles : [],
            'orderby'     => 'ID',
            'order'       => 'ASC',
            'fields'      => 'all_with_meta',
        ];

        if ( empty( $include ) ) {
            /**
             * Filter to increase or decrease per page user count if necessary
             *
             * @since 1.0.0
             *
             * @var int
             */
            $args['number'] = apply_filters( 'wemail_import_wp_users_per_page', 100 );
        }

        if ( ! empty( $include ) ) {
            $args['include'] = $include;
        }

        $this->user_query_after_id = $request->get_param( 'after_id' );

        add_action( 'pre_user_query', [ $this, 'pre_user_query' ] );

        $user_query = new \WP_User_Query( $args );
        $wp_users = $user_query->get_results();

        remove_action( 'pre_user_query', [ $this, 'pre_user_query' ] );

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

        return rest_ensure_response( $users );
    }

    /**
     * Hooked method for pre_user_query action
     *
     * @since 1.0.0
     *
     * @param object $query
     *
     * @return void
     */
    public function pre_user_query( $query ) {
        $query_after_id = absint( $this->user_query_after_id );

        if ( $query_after_id ) {
            $query->query_where .= ' AND ID > ' . $query_after_id;
        }
    }

}
