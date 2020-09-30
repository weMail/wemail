<?php

namespace WeDevs\WeMail\Core\Ecommerce\WooCommerce;

use WeDevs\WeMail\Traits\Singleton;

class WCIntegration {

    use Singleton;

    private $source = 'woocommerce';

    /**
     * Get status of woocommerce integration in wemail
     */

    public function status() {
        return [
            'type'                  => $this->source,
            'is_active'             => $this->is_woocommerce_activated(),
            'is_wemail_integrated'  => $this->is_woocommerce_connected_to_wemail(),
            'is_woocommerce_synced' => $this->is_woocommerce_synced_with_wemail(),
        ];
    }

    /**
     * @return bool
     */
    protected function is_woocommerce_connected_to_wemail() {
        $integrated = get_option( 'wemail_woocommerce_integrated' );

        return $integrated ? true : false;
    }

    /**
     * @return bool
     */
    protected function is_woocommerce_synced_with_wemail() {
        $synced = get_option( 'is_woocommerce_synced' );

        return $synced ? true : false;
    }

    /**
     * @return bool
     */
    protected function is_woocommerce_activated() {
        return class_exists( $this->source );
    }
}
