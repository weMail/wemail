<?php

namespace WeDevs\WeMail\Core\Subscriber;

use WeDevs\WeMail\Traits\Singleton;

class Subscriber {

    use Singleton;

    /**
     * Get a list of subscribers
     *
     * @since 1.0.0
     *
     * @param array $args
     *
     * @return array
     */
    public function all( $args = [] ) {
        return wemail()->api->subscribers()->query( $args )->get();
    }

    /**
     * Get data for a single subscriber
     *
     * @since 1.0.0
     *
     * @param string $id
     *
     * @return array
     */
    public function get( $id ) {
        $subscriber = wemail()->api->subscribers( $id )->get();

        return !empty( $subscriber['data'] ) ? $subscriber['data'] : null;
    }
}
