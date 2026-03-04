<?php

namespace WeDevs\WeMail\Rest\Resources\Ecommerce\WooCommerce;

use DateTimeZone;
use WeDevs\WeMail\Rest\Resources\JsonResource;

class CartResource extends JsonResource {

	/**
	 * Date format
	 */
	const DATE_FORMAT = 'Y-m-d h:i:s';

	/**
	 * Structure of cart
	 *
	 * @param $cart
	 *
	 * @return array
	 */
	public function blueprint( $cart ) {
		$cart_items = $cart->get_cart();
		$items      = array();

		foreach ( $cart_items as $cart_item_key => $cart_item ) {
			$product   = $cart_item['data'];
			$permalink = $product->get_permalink();
			$thumbnail = wp_get_attachment_image_url( $product->get_image_id() );

			$items[] = array(
				'cart_item_key' => $cart_item_key,
				'id'            => (string) $product->get_id(),
				'parent_id'     => (string) $product->get_parent_id(),
				'name'          => $product->get_name(),
				'quantity'      => $cart_item['quantity'],
				'total'         => floatval( $cart_item['line_total'] ),
				'thumbnail'     => $thumbnail ? $thumbnail : null,
				'categories'    => $product->get_category_ids(),
				'permalink'     => $permalink ? $permalink : null,
			);
		}

		return array(
			'items'       => $items,
			'total'       => floatval( $cart->get_total( 'raw' ) ),
			'subtotal'    => floatval( $cart->get_subtotal() ),
			'currency'    => get_woocommerce_currency(),
			'updated_at'  => current_time( self::DATE_FORMAT, true ),
			'source'      => 'woocommerce',
		);
	}

	/**
	 * Get cart with customer data and cart key
	 *
	 * @param \WC_Cart $cart
	 * @param string $cart_key
	 * @return array
	 */
	public static function with_customer( $cart, $cart_key = null ) {
		$data         = self::single( $cart );
		$current_user = wp_get_current_user();

		// Add cart key
		$data['cart_key'] = $cart_key;

		if ( $current_user->exists() ) {
			$data['customer'] = array(
				'id'         => (string) $current_user->ID,
				'first_name' => $current_user->first_name,
				'last_name'  => $current_user->last_name,
				'email'      => $current_user->user_email,
			);
		} else {
			// Try to get guest checkout data from WooCommerce session
			$customer = WC()->session->get( 'customer' );
			if ( $customer ) {
				$data['customer'] = array(
					'first_name' => isset( $customer['first_name'] ) ? $customer['first_name'] : null,
					'last_name'  => isset( $customer['last_name'] ) ? $customer['last_name'] : null,
					'email'      => isset( $customer['email'] ) ? $customer['email'] : null,
				);
			}
		}

		return $data;
	}
}
