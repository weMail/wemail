<?php
namespace WeDevs\WeMail;

use WeDevs\WeMail\Framework\Traits\Ajax as AjaxTrait;

class Ajax {

    use AjaxTrait;

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->add_ajax_action( 'get_country_states' );
    }

    /**
     * Get states/province/divisions for a country
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function get_country_states() {
        $this->verify_nonce();

        if ( empty( $_GET['country'] ) ) {
            $states = [];
        } else {
            $states = wemail_get_country_states( $_GET['country'] );
        }

        $this->send_success(['states' => $states]);
    }

}

