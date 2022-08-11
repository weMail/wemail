<?php

namespace WeDevs\WeMail\Rest;

use WeDevs\WeMail\RestController;
use WP_Query;

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
     * @return void
     * @since 1.0.0
     */
    public function register_routes() {
        $this->get( '/post-types', 'post_types', 'can_update_campaign' );
        $this->get( '/posts', 'posts', 'can_update_campaign' );
        $this->get( '/user-roles', 'user_roles', 'can_manage_settings' );
        $this->get( '/users', 'users', 'can_create_subscriber' );
        $this->get( '/products', 'products', 'can_update_campaign' );
        $this->get( '/post-types/(?P<post_type>[\w]+)/taxonomies/(?P<taxonomy>post_tag|category)', 'terms', 'can_update_campaign' );
    }

    /**
     * Get all public post types
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|mixed
     * @since 1.0.0
     */
    public function post_types( $request ) {
        $post_types       = [];
        $registered_types = get_post_types( [ 'public' => true ], 'objects' );

        foreach ( $registered_types as $name => $object ) {
            if ( $object->name === 'attachment' || $object->name === 'product' ) {
                continue;
            }

            $post_types[] = [
                'name'  => $object->name,
                'label' => $object->label,
            ];
        }

        return rest_ensure_response( $post_types );
    }

    /**
     * Get public posts
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|mixed
     * @since 1.0.0
     */
    public function posts( $request ) {
        $post_type   = $request->get_param( 'post_type' );
        $search      = $request->get_param( 's' );
        $tag_id      = $request->get_param( 'tag_id' );
        $category_id = $request->get_param( 'category_id' );
        $limit       = $request->get_param( 'limit' );

        $limit = empty( $limit ) ? 5 : $limit;

        $posts = [];

        $args = [
            'post_type'      => ! empty( $post_type ) ? $post_type : 'post',
            's'              => $search,
            'posts_per_page' => $limit,
            'post__not_in'   => get_option( 'sticky_posts' ),
        ];

        if ( ! empty( $category_id ) ) {
            $args['cat'] = $category_id;
        }

        if ( ! empty( $tag_id ) ) {
            $args['tag_id'] = $tag_id;
        }

        // The Query
        $query = new WP_Query( $args );

        // The Loop
        if ( $query->have_posts() ) {
            while ( $query->have_posts() ) {
                $query->the_post();

                $id = $query->post->ID;

                $posts [] = [
                    'id'         => $id,
                    'image'      => get_the_post_thumbnail_url( $id ),
                    'title'      => wp_strip_all_tags( $query->post->post_title ),
                    'postType'   => $query->post->post_type,
                    'postStatus' => $query->post->post_status,
                    'url'        => get_permalink( $id ),
                    'content'    => apply_filters( 'the_content', get_the_content() ),
                    'excerpt'    => wp_strip_all_tags( $query->post->post_excerpt ),
                    'meta'       => [
                        'tags'       => $this->get_tags( get_the_tags( $id ) ),
                        'postDate'   => get_the_date( 'Y-m-d', $id ),
                        'author'     => get_the_author(),
                        'categories' => $this->get_categories( get_the_category( $id ) ),
                    ],
                ];
            }

            wp_reset_postdata();
        }

        return rest_ensure_response( $posts );
    }

    /**
     * Get all user roles
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|mixed
     * @since 1.0.0
     */
    public function user_roles( $request ) {
        global $wp_roles;

        $user_roles = [];

        $roles = $wp_roles->get_names();

        foreach ( $roles as $name => $title ) {
            $user_roles[] = [
                'name'  => $name,
                'title' => $title,
            ];
        }

        return rest_ensure_response( [ 'data' => $user_roles ] );
    }

    /**
     * Get WordPress Users
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|mixed
     * @since 1.0.0
     */
    public function users( $request ) {
        $roles   = $request->get_param( 'roles' );
        $include = $request->get_param( 'include' );

        $args = [
            'role__in' => ! empty( $roles ) ? $roles : [],
            'orderby'  => 'ID',
            'order'    => 'ASC',
            'fields'   => 'all_with_meta',
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
        $wp_users   = $user_query->get_results();

        remove_action( 'pre_user_query', [ $this, 'pre_user_query' ] );

        $users = [];

        foreach ( $wp_users as $user ) {
            $users[] = [
                'ID'         => $user->ID,
                'first_name' => $user->get( 'first_name' ),
                'last_name'  => $user->get( 'last_name' ),
                'user_email' => $user->user_email,
                'user_login' => $user->user_login,
            ];
        }

        return rest_ensure_response( $users );
    }

    /**
     * Hooked method for pre_user_query action
     *
     * @param object $query
     *
     * @return void
     * @since 1.0.0
     */
    public function pre_user_query( $query ) {
        $query_after_id = absint( $this->user_query_after_id );

        if ( $query_after_id ) {
            $query->query_where .= ' AND ID > ' . $query_after_id;
        }
    }

    /**
     * Get terms
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response
     */
    public function terms( \WP_REST_Request $request ) {
        add_filter(
            'terms_clauses', function ( $clauses, $taxonomy, $args ) {
				global $wpdb;

				$clauses['join']  .= " INNER JOIN $wpdb->term_relationships AS r ON r.term_taxonomy_id = tt.term_taxonomy_id INNER JOIN $wpdb->posts AS p ON p.ID = r.object_id";
				$clauses['where'] .= " AND p.post_type = '" . esc_sql( $args['post_type'] ) . "' GROUP BY t.term_id";

				return $clauses;
			}, 10, 3
        );

        $post_type = $request->get_param( 'post_type' );
        $taxonomy  = $request->get_param( 'taxonomy' );

        $terms = get_terms(
            [
                'taxonomy'  => $taxonomy,
                'post_type' => $post_type,
            ]
        );

        return new \WP_REST_Response( $terms );
    }

    /** Get woo commerce products
     *
     * @param \WP_REST_Request $request
     *
     * @return mixed|\WP_REST_Response
     */
    public function products( $request ) {
        if ( ! class_exists( 'WooCommerce' ) ) {
            return rest_ensure_response(
                [
                    'data'    => (object) [],
                    'message' => __( 'Please install or active woocomerce plugin.', 'wemail' ),
                ]
            );
        }

        $args = [
            'limit' => 20,
            's'     => $request->get_param( 's' ),
        ];

        $products = [];

        $query = wc_get_products( $args );

        /** @var \WC_Product_Simple $product */
        foreach ( $query as $product ) {
            $id = $product->get_id();

            $post_thumb_id = get_post_thumbnail_id( $id );
            $image         = wemail_get_image_url( $post_thumb_id );

            $products[] = [
                'id'                => $product->get_id(),
                'name'              => $product->get_name(),
                'type'              => $product->get_type(),
                'rating'            => $product->get_average_rating(),
                'status'            => $product->get_status(),
                'image'             => $image,
                'description'       => $product->get_description(),
                'short_description' => $product->get_short_description(),
                'price'             => wc_price( $product->get_price() ),
                'read_more'         => get_permalink( $id ),
            ];
        }

        return rest_ensure_response(
            [
                'data'    => $products,
                'message' => __( 'You don\'t have any products on your store.', 'wemail' ),
            ]
        );
    }

    protected function get_tags( $tags ) {
        if ( ! is_array( $tags ) ) {
            return '';
        }

        return implode(
            ', ',
            array_map(
                function ( $tag ) {
                    return $tag->name;
                },
                $tags
            )
        );
    }

    protected function get_categories( $categories ) {
        return implode(
            ', ',
            array_map(
                function ( $category ) {
                    return $category->cat_name;
                },
                $categories
            )
        );
    }
}
