<?php

namespace WeDevs\WeMail\FrontEnd;

use WeDevs\WeMail\FrontEnd\Shortcodes;
use WeDevs\WeMail\Traits\Hooker;

class FrontEnd {

    use Hooker;

    public function __construct() {
        $this->add_action( 'template_redirect', 'init' );
    }

    public function init() {
        wemail_set_owner_api_key();

        new Scripts();
        new Shortcodes();
    }

}

