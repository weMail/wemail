<?php

namespace WeDevs\WeMail\Core\Sync\Subscriber;

use Stringy\StaticStringy;

class Subscriber {

    private $container = [
        'wp' => null,
        'erp' => null,
    ];

    public function __get( $prop ) {
        if ( array_key_exists( $prop, $this->container ) ) {
            if ( ! $this->container[ $prop ] ) {
                $integration = StaticStringy::upperCamelize( $prop );
                $class_fqn = "\\WeDevs\\WeMail\\Core\\Sync\\Subscriber\\$integration\\$integration";

                $this->container[ $prop ] = new $class_fqn();
            }

            return $this->container[ $prop ];
        }
    }

}
