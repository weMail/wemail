<?php

namespace WeDevs\WeMail\Core\Ecommerce\Platforms;

use EDD_Payment;
use WeDevs\WeMail\Core\Ecommerce\Settings;
use WeDevs\WeMail\Core\Sync\Ecommerce\RevenueTrack;
use WeDevs\WeMail\Rest\Resources\Ecommerce\EDD\CategoryResource;
use WeDevs\WeMail\Rest\Resources\Ecommerce\EDD\OrderResource;
use WeDevs\WeMail\Rest\Resources\Ecommerce\EDD\OrderResourceV3;
use WeDevs\WeMail\Rest\Resources\Ecommerce\EDD\ProductResource;
use WeDevs\WeMail\Traits\Singleton;
use WP_Post;
use WP_Query;

class EDD extends AbstractPlatform {

    use Singleton;

    protected $registered_shutdown = false;

    /**
     * @var null|array
     */
    protected $orders = [
        'trash'   => [],
        'restore' => [],
    ];

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
            return $this->orders_v3( $args );
        }

        $args = wp_parse_args(
            $args,
            [
                'posts_per_page' => isset( $args['limit'] ) ? intval( $args['limit'] ) : 50,
                'paged'          => isset( $args['page'] ) ? intval( $args['page'] ) : 1,
                'post_type'      => 'edd_payment',
                'post_status'    => [ 'publish', 'pending' ],
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

        $data = OrderResource::collection( $payments->get_posts() );

        return [
            'data'         => $data,
            'total'        => $payments->found_posts,
            'current_page' => intval( $args['paged'] ),
            'total_page'   => $payments->max_num_pages,
        ];
    }

    /**
     * Get orders from EDD in version 3
     *
     * @param array $args
     *
     * @return array
     */
    public function orders_v3( array $args = [] ) {
        $args = wp_parse_args(
            $args, [
                'number'  => isset( $args['limit'] ) ? intval( $args['limit'] ) : 50,
                'page'    => isset( $args['page'] ) ? intval( $args['page'] ) : 1,
                'type'    => [ 'sale', 'refund' ],
                'orderby' => 'date_modified',
            ]
        );

        if ( isset( $args['limit'] ) ) {
            unset( $args['limit'] );
        }

        if ( isset( $args['after_updated'] ) ) {
            $args['date_query'] = [
                [
                    'after'     => gmdate( 'Y-m-d H:i:s', (int) $args['after_updated'] ),
                    'column'    => 'date_modified',
                    'inclusive' => true,
                ],
            ];

            unset( $args['after_updated'] );
        }

        $data = edd_get_payments( $args );

        return [
            'data'         => OrderResourceV3::collection( $data ),
            'current_page' => intval( $args['page'] ),
            'total_page'   => empty( $data ) ? intval( $args['page'] ) : 'next',
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
        add_action( 'post_updated', [ $this, 'handle_product' ], 10, 2 );

        if ( $this->is_version_v3() ) {
            add_action( 'edd_refund_order', [ $this, 'handle_refund' ], 10, 2 );
            add_action( 'edd_update_payment_status', [ $this, 'sync_order_v3' ], 10, 3 );
            add_action( 'edd_complete_purchase', [ $this, 'completed_order_v3' ], 10, 2 );
        } else {
            add_action( 'after_delete_post', [ $this, 'delete_item' ], 10, 2 );
            add_action( 'edd_complete_purchase', [ $this, 'handle_order' ], 10, 2 );
            add_action( 'edd_update_payment_status', [ $this, 'handle_order' ] );
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
     * @param mixed ...$args
     *
     * @return void
     */
    public function handle_order( ...$args ) {
        if ( ! Settings::instance()->is_integrated() ) {
            return;
        }

        $payment_id            = $args[0];
        $is_from_complete_hook = count( $args ) === 2;

        $post = get_post( $payment_id );

        switch ( $post->post_status ) {
            case 'publish':
                // Is order completed and is not from payment_completed hook then we should stop
                if ( ! $is_from_complete_hook ) {
                    return;
                }
                break;
            case 'refunded':
                // If order has refunded then we should delete the refunded order since EDD version 2 doesn't have partial refund.
                $this->delete_item( $payment_id, $post );

                return;
        }

        if ( ! Settings::instance()->is_enabled() ) {
            return;
        }

        $this->sync_order( OrderResource::single( $post ) );
    }

    /**
     * Handle order complete
     *
     * @param $order_id
     * @param EDD_Payment $payment
     *
     * @return void
     */
    public function completed_order_v3( $order_id, EDD_Payment $payment ) {
        if ( ! Settings::instance()->is_enabled() ) {
            return;
        }

        $this->sync_order( OrderResourceV3::single( $payment ) );
    }

    /**
     * Handling order sync on edd version 3
     *
     * @param $payment_id
     * @param $new_status
     * @param $old_status
     *
     * @return void
     */
    public function sync_order_v3( $payment_id, $new_status, $old_status ) {
        if ( ! $this->registered_shutdown ) {
            add_action( 'shutdown', [ $this, 'on_shutdown' ] );
            $this->registered_shutdown = true;
        }

        if ( $new_status === 'trash' ) {
            $this->orders['trash'][] = $payment_id;

            return;
        }

        if ( $old_status === 'trash' ) {
            $this->orders['restore'][] = $payment_id;

            return;
        }

        // Handled `complete` status on the [self::completed_order_v3] method
        if ( $new_status === 'complete' ) {
            return;
        }

        if ( ! Settings::instance()->is_enabled() ) {
            return;
        }

        $payment = edd_get_payment( $payment_id );

        if ( ! $payment ) {
            return;
        }

        $this->sync_order( OrderResourceV3::single( $payment ) );
    }

    /**
     * Sync order with weMail
     *
     * @param array $payload
     *
     * @return void
     */
    public function sync_order( array $payload ) {
        $module = wemail()->api
            ->send_json()
            ->ecommerce();

        if ( $payload['type'] === 'order' ) {
            RevenueTrack::track_id( $payload );
            $module->orders( $payload['id'] );
        } else {
            $module->orders( $payload['parent_id'] )->refunds( $payload['id'] );
        }

        $module->put( $payload );
    }

    /**
     * Delete order
     *
     * @param $post_id
     * @param WP_Post $post
     */
    public function delete_item( $post_id, WP_Post $post ) {
        if ( ! preg_match( '/^download|edd_payment$/', $post->post_type ) ) {
            return;
        }

        if ( ! Settings::instance()->is_integrated() ) {
            return;
        }

        if ( $post->post_type === 'download' ) {
            wemail()->api
                ->ecommerce()
                ->products( $post_id )
                ->send_json()
                ->post(
                    [
                        '_method' => 'delete',
                    ]
                );
        }

        if ( $post->post_type === 'edd_payment' ) {
            wemail()->api
                ->ecommerce()
                ->orders( $post_id )
                ->send_json()
                ->post(
                    [
                        '_method' => 'delete',
                    ]
                );
        }
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
     * Is edd version 3 or higher
     *
     * @return bool|int
     */
    public function is_version_v3() {
        return version_compare( '3', EDD_VERSION, '<=' );
    }

    /**
     * Handle product upsert
     *
     * @param $post_id
     * @param WP_Post $post
     *
     * @return void
     */
    public function handle_product( $post_id, WP_Post $post ) {
        wemail()->api
            ->ecommerce()
            ->products( $post_id )
            ->send_json()
            ->put( ProductResource::single( $post ) );
    }

    /**
     * Handle refund
     *
     * @param $order_id
     * @param $refund_id
     *
     * @return void
     */
    public function handle_refund( $order_id, $refund_id ) {
        if ( ! Settings::instance()->is_enabled() ) {
            return;
        }

        wemail()->api
            ->send_json()
            ->ecommerce()
            ->orders( $order_id )
            ->refunds( $refund_id )
            ->put( OrderResourceV3::single( new EDD_Payment( $refund_id ) ) );
    }

    /**
     * Delete multiple payments at the end of execution of WordPress for EDD 3
     *
     * @return void
     */
    public function on_shutdown() {
        if ( ! Settings::instance()->is_integrated() ) {
            return;
        }

        $trash = array_unique( $this->orders['trash'] );

        foreach ( $trash as $id ) {
            $this->delete_version_3_payment( edd_get_payment( $id ) );
        }

        $restore = array_unique( $this->orders['restore'] );

        foreach ( $restore as $id ) {
            $payment = edd_get_payment( $id );
            if ( ! $payment ) {
                continue;
            }

            $this->sync_order( OrderResourceV3::single( $payment ) );
        }
    }

    /**
     * Delete payment or refund item
     *
     * @param $payment_id
     *
     * @return void
     */
    protected function delete_version_3_payment( $payment_id ) {
        $payment = edd_get_payment( $payment_id );

        if ( ! $payment ) {
            return;
        }

        $module = wemail()->api->ecommerce();

        if ( $payment->parent_payment ) {
            $module->orders( $payment->parent_payment )->refunds( $payment->transaction_id );
        } else {
            $module->orders( $payment->transaction_id );
        }

        $module->post( [ '_method' => 'delete' ] );
    }
}
