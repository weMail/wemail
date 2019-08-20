<?php

namespace WeDevs\WeMail\FrontEnd;

use WeDevs\WeMail\FrontEnd\Shortcodes;
use WeDevs\WeMail\Traits\Hooker;

class FrontEnd {

    use Hooker;

    public function __construct() {
        register_widget( '\WeDevs\WeMail\FrontEnd\Widget' );
        $this->add_action( 'template_redirect', 'init' );
        new FormOptIn();
    }

    public function init() {
        wemail_set_owner_api_key();

        new Scripts();
        new Shortcodes();
    }

}

