<?php

namespace WeDevs\WeMail\Core\Ecommerce\Hooks;

use WeDevs\WeMail\Traits\Singleton;

class Ecommerce {

    use Singleton;

    /**
     * Registering ecommerce platforms hooks
     */
    public function boot() {
        foreach ( wemail()->ecommerce->getPlatforms() as $platform ) {
            if ( $platform::instance()->is_active() ) {
                $platform::instance()->register_hooks();
            }
        }
    }
}
