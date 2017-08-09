<?php
namespace WeDevs\WeMail;

use WeDevs\WeMail\Framework\Traits\AjaxHandler;

class Ajax {

    use AjaxHandler;

    public function __construct() {
        $this->add_action( 'get_something' );
    }

    public function get_something() {
        $this->verify_nonce();

        $this->send_success();
    }
}
