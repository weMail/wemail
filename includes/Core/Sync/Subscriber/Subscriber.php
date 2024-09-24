<?php

namespace WeDevs\WeMail\Core\Sync\Subscriber;

use WeDevs\WeMail\Traits\Stringy;

class Subscriber {
    use Stringy;

    private $container = array(
        'wp' => null,
        'erp' => null,
    );

    public function __get( $prop ) {
        if ( array_key_exists( $prop, $this->container ) ) {
            if ( ! $this->container[ $prop ] ) {
                $integration = $this->upperCamelize( $prop );
                $class_fqn = "\\WeDevs\\WeMail\\Core\\Sync\\Subscriber\\$integration\\$integration";

                $this->container[ $prop ] = new $class_fqn();
            }

            return $this->container[ $prop ];
        }
    }
}
