<?php

namespace WeDevs\WeMail\Core\Ecommerce\EDD;

use WeDevs\WeMail\Traits\Singleton;
use WP_User_Query;

class EDDSettings {

    use Singleton;

    public function get() {
        return [
            'currency' => edd_get_currency(),
            'currency_symbol' => edd_currency_symbol(),
        ];
    }
}
