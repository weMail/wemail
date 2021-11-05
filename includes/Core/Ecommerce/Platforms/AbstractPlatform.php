<?php

namespace WeDevs\WeMail\Core\Ecommerce\Platforms;

use WeDevs\WeMail\Core\Ecommerce\Settings;

abstract class AbstractPlatform implements PlatformInterface {
    /**
     * Is integrated
     *
     * @return bool
     */
    public function is_integrated() {
        return Settings::instance()->is_integrated() && Settings::instance()->platform() === $this->get_name();
    }
}
