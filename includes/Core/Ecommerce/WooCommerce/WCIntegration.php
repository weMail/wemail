<?php

namespace WeDevs\WeMail\Core\Ecommerce\WooCommerce;

use WeDevs\WeMail\Traits\Singleton;

class WCIntegration {

    use Singleton;

    /**
     * Get status of woocommerce integration in wemail
     */

    public function status() {
        return [
            'is_active' => $this->is_woocommerce_activated(),
            'is_wemail_integrated' => $this->is_woocommerce_connected_to_wemail(),
        ];
    }

    /**
     * @return bool
     */
    protected function is_woocommerce_connected_to_wemail() {
        $source = get_option( 'wemail_ecommerce_source' );

        if ($source == 'woocommerce') {
            return true;
        }

        return false;
    }

    /**
     * @return bool
     */
    protected function is_woocommerce_activated() {
        return class_exists( 'woocommerce' );
    }
}
