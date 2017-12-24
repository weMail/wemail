<?php

namespace WeDevs\WeMail\Modules\Subscriber;

use WeDevs\WeMail\Framework\Module;

class Subscriber extends Module {

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
    }

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
