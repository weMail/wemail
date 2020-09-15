<?php

namespace WeDevs\WeMail\Core\Sync;

use Stringy\StaticStringy;
use WeDevs\WeMail\Traits\Singleton;

class Sync {

    use Singleton;

    private $container = [
        'subscriber' => null,
    ];

    public function __get( $prop ) {
        if ( array_key_exists( $prop, $this->container ) ) {
            if ( ! $this->container[ $prop ] ) {
                $class_name = StaticStringy::upperCamelize( $prop );
                $class_fqn = "\\WeDevs\\WeMail\\Core\\Sync\\$class_name\\$class_name";

                $this->container[ $prop ] = new $class_fqn();
            }

            return $this->container[ $prop ];
        }
    }
}
