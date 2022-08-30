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
        $product   = $order_item->get_product();
        $permalink = $product->get_permalink();
        $thumbnail = wp_get_attachment_image_url( $product->get_image_id() );

        return [
            'id'         => (string) $product->get_id(),
            'parent_id'  => (string) $product->get_parent_id(),
            'name'       => $order_item->get_name(),
            'quantity'   => $order_item->get_quantity(),
            'total'      => floatval( $order_item->get_total() ),
            'thumbnail'  => $thumbnail ? $thumbnail : null,
            'categories' => $product->get_category_ids(),
            'permalink'  => $permalink ? $permalink : null,
        ];
    }
}
