<?php

namespace WeDevs\WeMail\Core\Settings;

use WeDevs\WeMail\Traits\Core;
use WeDevs\WeMail\Traits\Stringy;

class Settings {

    use Core;
    use Stringy;

    /**
     * Get site settings
     *
     * @since 1.0.0
     *
     * @param string $name
     *
     * @return array|string
     */
    public function get( $name ) {
        $name = $this->underscored( $name );

        $settings = wemail()->api->settings()->$name()->get();

        return $this->data( $settings );
    }
}
