<?php

namespace WeDevs\WeMail\Core\Ecommerce\Platforms;

use WeDevs\WeMail\Rest\Resources\Ecommerce\EDD\CategoryResource;
use WP_Post;
use WeDevs\WeMail\Traits\Singleton;
use WeDevs\WeMail\Core\Ecommerce\Settings;
use WeDevs\WeMail\Rest\Resources\Ecommerce\EDD\OrderResource;
use WeDevs\WeMail\Rest\Resources\Ecommerce\EDD\ProductResource;

class EDD extends AbstractPlatform {

    use Singleton;

    /**
     * Currency name
     *
     * @return string
     */
	public function currency() {
		return edd_get_currency();
	}

    /**
     * Currency symbol
     *
     * @return string
     */
	public function currency_symbol() {
		return edd_currency_symbol();
	}

    /**
     * Get products from edd
     *
     * @param array $args
     *
     * @return array
     */
	public function products( array $args = [] ) {
	    $args = wp_parse_args(
            $args, [
				'page' => 1,
				'limit' => 50,
			]
        );

	    $query = new \WP_Query(
            array(
				'post_type'         => 'download',
				'posts_per_page'    => $args['limit'],
				'paged'             => $args['page'],
            )
        );

	    return [
	        'data' => ProductResource::collection( $query->get_posts() ),
            'total' => $query->found_posts,
            'current_page' => intval( $args['page'] ),
            'total_page' => $query->max_num_pages,
        ];
	}

    /**
     * Get orders from edd
     *
     * @param array $args
     *
     * @return array
     */
	public function orders( array $args = [] ) {
        $args = wp_parse_args(
            $args,
            [
                'posts_per_page'    => isset( $args['limit'] ) ? intval( $args['limit'] ) : 50,
                'paged'             => isset( $args['page'] ) ? intval( $args['page'] ) : 1,
                'post_type'         => 'edd_payment',
                'post_status'       => 'any',
            ]
        );

        if ( isset( $args['after_updated'] ) ) {
            $args['date_query'] = [
                [
                    'column' => 'post_modified_gmt',
                    'after' => gmdate( 'Y-m-d H:i:s', $args['after_updated'] ),
                ],
            ];
        }

        $payments = new \WP_Query( $args );

        return [
            'data' => OrderResource::collection( $payments->get_posts() ),
            'total' => $payments->found_posts,
            'current_page' => intval( $args['paged'] ),
            'total_page' => $payments->max_num_pages,
        ];
    }

    /**
     * Get categories from EDD store
     *
     * @param array $args
     *
     * @return array
     */
    public function categories( array $args = [] ) {
        $terms = get_terms(
            [
				'taxonomy'   => 'download_category',
				'hide_empty' => false,
			]
        );

        return [
            'data' => CategoryResource::collection( $terms ),
        ];
    }

	public function register_hooks() {
        add_action( 'edd_update_payment_status', [ $this, 'handle_order' ] );
        add_action( 'after_delete_post', [ $this, 'delete_order' ], 10, 2 );
	}

    public function is_active() {
        return class_exists( 'EDD_API' );
    }

    public function handle_order( $order_id ) {
        if ( ! Settings::instance()->is_enabled() ) {
            return;
        }

        $post = get_post( $order_id );

        wemail()->api
            ->send_json()
            ->ecommerce()
            ->orders( $order_id )
            ->put( OrderResource::single( $post ) );
    }

    /**
     * Delete order
     *
     * @param $order_id
     * @param WP_Post $post
     */
    public function delete_order( $order_id, WP_Post $post ) {
        if ( $post->post_type !== 'edd_payment' ) {
            return;
        }

        if ( ! Settings::instance()->is_integrated() ) {
            return;
        }

        wemail()->api
            ->ecommerce()
            ->orders( $order_id )
            ->post(
                [
					'_method' => 'delete',
				]
            );
    }

    public function get_name() {
        return 'edd';
    }
}
