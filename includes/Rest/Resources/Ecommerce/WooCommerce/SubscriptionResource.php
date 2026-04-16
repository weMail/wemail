<?php

namespace WeDevs\WeMail\Rest\Resources\Ecommerce\WooCommerce;

use DateTimeZone;
use WeDevs\WeMail\Rest\Resources\JsonResource;

class SubscriptionResource extends JsonResource {

    /**
     * Date format
     */
    const DATE_FORMAT = 'Y-m-d';

    /**
     * Structure of subscription
     *
     * @param \WC_Subscription $subscription
     *
     * @return array
     */
    public function blueprint( $subscription ) {
        $items = array_filter(
            $subscription->get_items(), function ( $item ) {
                return $item->get_product_id();
            }
        );

        return array(
            'id'                => (string) $subscription->get_id(),
            'status'            => $subscription->get_status(),
            'customer_id'       => $this->get_customer_id( $subscription->get_billing_email() ),
            'customer'          => $this->customer( $subscription ),
            'total'             => floatval( $subscription->get_total() ),
            'product_ids'       => $this->get_product_ids( $items ),
            'categories'        => $this->get_category_ids( $items ),
            'billing_period'    => $subscription->get_billing_period(),
            'billing_interval'  => intval( $subscription->get_billing_interval() ),
            'next_payment_date' => $this->format_date( $subscription->get_date( 'next_payment' ) ),
            'created_at'        => $subscription->get_date_created() ? $subscription->get_date_created()->setTimezone( new DateTimeZone( 'UTC' ) )->format( self::DATE_FORMAT ) : null,
            'updated_at'        => $subscription->get_date_modified() ? $subscription->get_date_modified()->setTimezone( new DateTimeZone( 'UTC' ) )->format( self::DATE_FORMAT ) : null,
        );
    }

    /**
     * Transform customer data
     *
     * @param \WC_Subscription $subscription
     *
     * @return array
     */
    protected function customer( $subscription ) {
        return array(
            'email'      => $subscription->get_billing_email(),
            'first_name' => $subscription->get_billing_first_name(),
        );
    }

    /**
     * Get customer ID
     *
     * @param string $email
     *
     * @return string
     */
    protected function get_customer_id( $email ) {
        return md5( trim( strtolower( $email ) ) );
    }

    /**
     * Get product IDs from subscription items
     *
     * @param array $items
     *
     * @return array
     */
    protected function get_product_ids( $items ) {
        $product_ids = array();

        foreach ( $items as $item ) {
            $product_ids[] = (string) $item->get_product_id();
        }

        return array_unique( $product_ids );
    }

    /**
     * Get category IDs from subscription items
     *
     * @param array $items
     *
     * @return array
     */
    protected function get_category_ids( $items ) {
        $category_ids = array();

        foreach ( $items as $item ) {
            $product = $item->get_product();

            if ( ! $product ) {
                continue;
            }

            $cats = $product->get_category_ids();

            foreach ( $cats as $cat_id ) {
                $category_ids[] = (string) $cat_id;
            }
        }

        return array_values( array_unique( $category_ids ) );
    }

    /**
     * Format a subscription date string
     *
     * @param string $date_string
     *
     * @return string|null
     */
    protected function format_date( $date_string ) {
        if ( empty( $date_string ) || $date_string === '0' ) {
            return null;
        }

        try {
            $date = new \DateTime( $date_string, new DateTimeZone( 'UTC' ) );
            return $date->format( self::DATE_FORMAT );
        } catch ( \Exception $e ) {
            return null;
        }
    }
}
