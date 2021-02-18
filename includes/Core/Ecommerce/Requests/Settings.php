<?php

namespace WeDevs\WeMail\Core\Ecommerce\Requests;

use WeDevs\WeMail\Traits\Singleton;

class Settings {

    use Singleton;

    public function update( $request ) {
        return wemail()->api->ecommerce()->settings()->put( $request );
    }
}
