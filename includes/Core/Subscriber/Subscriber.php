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

    /**
     * Create a subscriber
     *
     * @since 1.0.0
     *
     * @param array $data
     *
     * @return array|null
     */
    public function create( $data ) {
        $subscriber = wemail()->api->subscribers()->post( $data );

        return !empty( $subscriber['data'] ) ? $subscriber['data'] : null;
    }

    /**
     * Update a subscriber
     *
     * @since 1.0.0
     *
     * @param string $id
     * @param array  $data
     *
     * @return array|null
     */
    public function update( $id, $data ) {
        $subscriber = wemail()->api->subscribers( $id )->put( $data );

        return !empty( $subscriber['data'] ) ? $subscriber['data'] : null;
    }

    /**
     * Subscribe to lists
     *
     * @since 1.0.0
     *
     * @param string $id
     * @param array  $list_ids
     *
     * @return array|null
     */
    public function subscribe_to_lists( $id, $list_ids ) {
        $data = [
            'lists' => $list_ids
        ];

        $subscriber = wemail()->api->subscribers( $id )->subscribe_to_lists()->put( $data );

        return !empty( $subscriber['data'] ) ? $subscriber['data'] : null;
    }

}
