<?php

namespace WeDevs\WeMail\Core\Sync\Ecommerce;

use WeDevs\WeMail\Traits\Hooker;

class RevenueTrack {

    use Hooker;

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->add_action( 'template_redirect', 'wemail_set_revenue_cookie' );
    }


    /**
     * @return bool
     */
    public function wemail_set_revenue_cookie() {
        if ( ! isset( $_GET['campaign_id'] ) ) {
            return true;
        }

        if ( ! class_exists( 'WooCommerce' ) && ! class_exists( 'Easy_Digital_Downloads' ) ) {
            return true;
        }

        setcookie( 'wemail_campaign_revenue', sanitize_text_field( wp_unslash( $_GET['campaign_id'] ) ), strtotime( '+2 day' ), '/' );

        return true;
    }
}
