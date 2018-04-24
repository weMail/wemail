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
            'lists' => $list_ids
        ];

        $subscriber = wemail()->api->subscribers( $id )->subscribe_to_lists()->put( $data );

        return $this->data( $subscriber );
    }

    /**
     * Import or update WP user as weMail subscriber
     *
     * @since 1.0.0
     *
     * @param int $user_id
     *
     * @return void
     */
    public function update_wp_subscriber( $user_id ) {
        $user = $this->syncable_wp_user( $user_id );

        if (! $user) {
            return;
        }

        // Non-admin users can register or update their own profile
        // In that case, we have to use the owner's key to import
        // his/her profile
        wemail_set_owner_api_key( false );

        $data = [
            'wp_users' => [ $user_id ]
        ];

        wemail()->api->import()->wp_users()->post( $data );
    }

    /**
     * Delete WP user that is an weMail subscriber
     *
     * @since 1.0.0
     *
     * @param int $user_id
     *
     * @return void
     */
    public function delete_wp_subscriber( $user_id ) {
        $user = $this->syncable_wp_user( $user_id );

        if (! $user) {
            return;
        }

        // Non-admin users can register or update their own profile
        // In that case, we have to use the owner's key to import
        // his/her profile
        wemail_set_owner_api_key( false );

        $subscriber = wemail()->subscriber->get( $user->user_email );

        if ( $subscriber ) {
            wemail()->subscriber->delete( $subscriber['id'], true );
        }
    }

    /**
     * Checks sync settings
     *
     * @since 1.0.0
     *
     * @param int $user_id
     *
     * @return WP_User|false WP_User object on success, false on failure.
     */
    private function syncable_wp_user( $user_id ) {
        $user = get_userdata( $user_id );

        $settings = get_option( 'wemail_wp_users_sync' );

        if ( empty( $settings ) || empty( $settings['auto_sync'] ) || ! wemail_validate_boolean( $settings['auto_sync'] ) ) {
            return;
        }

        if ( empty( $settings['user_roles'] ) || ! is_array( $settings['user_roles'] ) ) {
            return;
        }

        $should_sync = false;

        foreach ( $user->roles as $role ) {
            if ( in_array( $role, $settings['user_roles'] ) ) {
                $should_sync = true;
                break;
            }
        }

        if ( ! $should_sync ) {
            return;
        }

        return $user;
    }

}
