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
            'type' => $this->source,
            'is_active' => $this->is_woocommerce_activated(),
            'is_wemail_integrated' => $this->is_woocommerce_connected_to_wemail()
        ];
    }

    /**
     * @return bool
     */
    protected function is_woocommerce_connected_to_wemail() {
        $integrated = get_option( 'wemail_ecommerce_integrated' );

        return $integrated ? true : false;
    }

    /**
     * @return bool
     */
    protected function is_woocommerce_activated() {
        return class_exists( $this->source );
    }
}
