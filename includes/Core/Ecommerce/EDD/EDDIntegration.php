<?php

namespace WeDevs\WeMail\Core\Ecommerce\EDD;

use WeDevs\WeMail\Traits\Singleton;

class EDDIntegration {

    use Singleton;

    private $source = 'edd';

    /**
     * Get status of edd integration in wemail
     */

    public function status() {
        return [
            'type'                  => $this->source,
            'is_active'             => $this->is_edd_activated(),
            'is_wemail_integrated'  => $this->is_edd_connected_to_wemail(),
            'is_edd_synced'         => $this->is_edd_synced_with_wemail(),
        ];
    }

    /**
     * @return bool
     */
    protected function is_edd_connected_to_wemail() {
        $integrated = get_option( 'wemail_edd_integrated' );

        return $integrated ? true : false;
    }

    /**
     * @return bool
     */
    protected function is_edd_synced_with_wemail() {
        $synced = get_option( 'is_edd_synced' );

        return $synced ? true : false;
    }

    /**
     * @return bool
     */
    protected function is_edd_activated() {
        return class_exists( 'Easy_Digital_Downloads' );
    }
}
