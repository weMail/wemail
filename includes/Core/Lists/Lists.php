<?php

namespace WeDevs\WeMail\Core\Lists;

use WeDevs\WeMail\Traits\Singleton;

class Lists {

    use Singleton;

    /**
     * Get a list of lists
     *
     * @since 1.0.0
     *
     * @param array $query
     *
     * @return array
     */
    public function all( $query = [] ) {
        return wemail()->api->lists()->query( $query )->get();
    }

    /**
     * Get all lists
     *
     * id-name paired items
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function items() {
        return wemail()->api->lists()->items()->get();
    }

    /**
     * Get a single list
     *
     * @since 1.0.0
     *
     * @param integer $id
     *
     * @return array
     */
    public function get( $id ) {
        $list = wemail()->api->lists( $id )->get();

        return !empty( $list['data'] ) ? $list['data'] : null;
    }

}
