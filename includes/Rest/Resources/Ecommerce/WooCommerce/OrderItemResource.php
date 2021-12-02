<?php

namespace WeDevs\WeMail\Rest\Resources\Ecommerce\WooCommerce;

use WeDevs\WeMail\Rest\Resources\JsonResource;

class OrderItemResource extends JsonResource {

    protected $reset_keys = true;

	/**
	 * @inheritDoc
	 */
	public function blueprint( $order_item ) {
        /** @var \WC_Order_Item_Product $order_item */
        $product = $order_item->get_product();

        return [
            'id'        => $order_item->get_product_id(),
            'name'      => $order_item->get_name(),
            'quantity'  => $order_item->get_quantity(),
            'total'     => floatval( $order_item->get_total() ),
            'thumbnail' => $product ? wp_get_attachment_image_url( $product->get_image_id() ) : null,
            'categories' => $product ? $product->get_category_ids() : [],
            'permalink' => $product ? $product->get_permalink() : null,
        ];
	}
}
