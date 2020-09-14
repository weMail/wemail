<?php

namespace WeDevs\WeMail\Core\Subscriber;

use WeDevs\WeMail\Traits\Core;

class Subscriber {

    use Core;

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
        $subscribers = wemail()->api->subscribers()->query( $args )->get();

        if ( ! is_wp_error( $subscribers ) ) {
            return $subscribers;
        }

        return null;
    }

    /**
     * Get data for a single subscriber
     *
     * @since 1.0.0
     *
     * @param string $id Subscriber id or email
     *
     * @return array
     */
    public function get( $id_or_email ) {
        $subscriber = wemail()->api->subscribers( $id_or_email )->get();

        return $this->data( $subscriber );
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

        return $this->data( $subscriber );
    }

    /**
     * Create update a subscriber
     *
     * @param $data
     *
     * @return array|null
     */
    public function createOrUpdate( $data ) {
        $response = wemail()->api->subscribers()->put( $data );

        return $this->data( $response );
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

        return $this->data( $subscriber );
    }

    /**
     * Delete a subscriber
     *
     * @since 1.0.0
     *
     * @param string $id
     * @param boolean $permanent
     *
     * @return int|null
     */
    public function delete( $id, $permanent = false ) {
        $args = [];

        if ( $permanent ) {
            $args['permanent'] = true;
        }

        $response = wemail()->api->subscribers( $id )->delete( $args );

        if ( ! is_wp_error( $response ) && ! empty( $response['deleted'] ) ) {
            return $response['deleted'];
        }

        return null;
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
            'lists' => $list_ids,
        ];

        $subscriber = wemail()->api->subscribers( $id )->subscribe_to_lists()->put( $data );

        return $this->data( $subscriber );
    }

}
