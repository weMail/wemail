<?php

namespace WeDevs\WeMail\Core\Sync;

use WeDevs\WeMail\Traits\Singleton;
use WeDevs\WeMail\Traits\Stringy;

class Sync {

    use Singleton, Stringy;

    private $container = [
        'subscriber' => null,
    ];

    public function __get( $prop ) {
        if ( array_key_exists( $prop, $this->container ) ) {
            if ( ! $this->container[ $prop ] ) {
                $class_name = $this->upperCamelize( $prop );
                $class_fqn = "\\WeDevs\\WeMail\\Core\\Sync\\$class_name\\$class_name";

                $this->container[ $prop ] = new $class_fqn();
            }

            return $this->container[ $prop ];
        }
    }
}
