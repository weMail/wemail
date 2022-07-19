<?php

namespace WeDevs\WeMail\Core\Ecommerce\Platforms;

use WP_Post;
use WP_Query;
use WeDevs\WeMail\Traits\Singleton;
use WeDevs\WeMail\Core\Ecommerce\Settings;
use WeDevs\WeMail\Core\Sync\Ecommerce\RevenueTrack;
use WeDevs\WeMail\Rest\Resources\Ecommerce\EDD\OrderResource;
use WeDevs\WeMail\Rest\Resources\Ecommerce\EDD\OrderResourceV3;
use WeDevs\WeMail\Rest\Resources\Ecommerce\EDD\ProductResource;
use WeDevs\WeMail\Rest\Resources\Ecommerce\EDD\CategoryResource;

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
                'page'  => 1,
                'limit' => 50,
            ]
        );

        $query = new WP_Query(
            array(
                'post_type'      => 'download',
                'posts_per_page' => $args['limit'],
                'paged'          => $args['page'],
            )
        );

        return [
            'data'         => ProductResource::collection( $query->get_posts() ),
            'total'        => $query->found_posts,
            'current_page' => intval( $args['page'] ),
            'total_page'   => $query->max_num_pages,
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
        if ( $this->is_version_v3() ) {
            return $this->order_v3( $args );
        }

        $args = wp_parse_args(
            $args,
            [
                'posts_per_page' => isset( $args['limit'] ) ? intval( $args['limit'] ) : 50,
                'paged'          => isset( $args['page'] ) ? intval( $args['page'] ) : 1,
                'post_type'      => 'edd_payment',
                'post_status'    => 'any',
            ]
        );

        if ( isset( $args['after_updated'] ) ) {
            $args['date_query'] = [
                [
                    'column' => 'post_modified_gmt',
                    'after'  => gmdate( 'Y-m-d H:i:s', $args['after_updated'] ),
                ],
            ];
        }

        $payments = new WP_Query( $args );

        return [
            'data'         => OrderResource::collection( $payments->get_posts() ),
            'total'        => $payments->found_posts,
            'current_page' => intval( $args['paged'] ),
            'total_page'   => $payments->max_num_pages,
        ];
    }

    public function order_v3( array $args = [] ) {
        $args = wp_parse_args( $args, [
            'number' => isset( $args['limit'] ) ? intval( $args['limit'] ) : 50,
            'page'   => isset( $args['page'] ) ? intval( $args['page'] ) : 1,
        ] );

        if ( isset( $args['after_updated'] ) ) {
            $args['start-date'] = gmdate( 'Y-m-d H:i:s', $args['after_updated'] );
        }

        return [
            'data' => OrderResourceV3::collection(edd_get_payments($args)),
            'current_page' => intval($args['page'])
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

    /**
     * Register hooks
     *
     * @return void
     */
    public function register_hooks() {
        if ( $this->is_version_v3() ) {
            add_action( 'edd_update_payment_status', [ $this, 'handle_order_v3' ] );
        } else {
            add_action( 'edd_update_payment_status', [ $this, 'handle_order' ] );
            add_action( 'after_delete_post', [ $this, 'delete_order' ], 10, 2 );
        }
    }

    /**
     * Check edd plugin is active or not
     *
     * @return bool
     */
    public function is_active() {
        return defined( 'EDD_VERSION' );
    }

    /**
     * Handle edd order
     *
     * @param $payment_id
     *
     * @return void
     */
    public function handle_order( $payment_id ) {
        if ( ! Settings::instance()->is_enabled() ) {
            return;
        }

        $post = get_post( $payment_id );

        $this->sync_order( $payment_id, OrderResource::single( $post ) );
    }

    /**
     * Handling order sync on edd version 3
     *
     * @param $payment_id
     *
     * @return void
     */
    public function handle_order_v3( $payment_id ) {
        $payment = edd_get_payment( $payment_id );

        if ( ! $payment ) {
            return;
        }

        $this->sync_order( $payment_id, OrderResourceV3::single( $payment ) );
    }

    /**
     * Sync order with weMail
     *
     * @param $id
     * @param array $payload
     *
     * @return void
     */
    public function sync_order( $id, array $payload ) {
        RevenueTrack::track_id( $payload );

        wemail()->api
            ->send_json()
            ->ecommerce()
            ->orders( $id )
            ->put( $payload );
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

    /**
     * Get integration name
     *
     * @return string
     */
    public function get_name() {
        return 'edd';
    }

    /**
     * Is edd version 3
     *
     * @return bool|int
     */
    public function is_version_v3() {
        return version_compare( '3', EDD_VERSION, '<=' );
    }
}
