<?php

namespace WeDevs\WeMail\Core\Sync\Subscriber\Wp;

class Wp {

    /**
     * Sync settings
     *
     * @var array
     *
     * @since 1.0.0
     */
    private $settings;

    /**
     * Sync settings
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function settings() {
        if ( ! isset( $this->settings['auto_sync'] ) ) {
            $defaults = [
                'auto_sync' => false,
                'user_roles' => [],
            ];

            $settings = get_option( 'wemail_sync_subscriber_wp', [] );

            $this->settings = wp_parse_args( $settings, $defaults );
        }

        return $this->settings;
    }

    /**
     * Checks if syncing enabled
     *
     * @since 1.0.0
     *
     * @return bool
     */
    public function is_active() {
        $settings = $this->settings();

        if ( ! empty( $settings['auto_sync'] ) && wemail_validate_boolean( $settings['auto_sync'] ) ) {
            return true;
        }

        return false;
    }

    /**
     * Create subscribers
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function create( $user_ids = [] ) {
        if ( ! $this->is_active() ) {
            return;
        }

        /**
         * When we save 'WP Users Sync' settings $user_ids is empty.
         * When one or more new users register, user_register hook fires
         * and we have $user_ids. In that case, we'll check syncable users first.
         */
        if ( ! empty( $user_ids ) ) {
            $users = $this->filter_syncable_users( $user_ids );

            if ( empty( $users ) ) {
                return;
            }
        }

        $users = array_map(
            function( $user ) {
                return array(
                    'full_name' => $user->data->display_name,
                    'email' => $user->data->user_email,
                );
            },
            $users
        );

        /**
         * Non-admin users can register or update their own profile.
         * In that case, we have to use the owner's key to import
         * his/her profile
         */
        wemail_set_owner_api_key( false );

        $post_data = [
            'users' => $users,
        ];

        wemail()->api->sync()->subscribers()->wp()->subscribe()->post( $post_data );
    }

    /**
     * Update subscribers
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function update( $user_ids ) {
        if ( ! $this->is_active() ) {
            return;
        }

        $users = $this->filter_syncable_users( $user_ids );

        if ( empty( $users ) ) {
            return;
        }

        wemail_set_owner_api_key( false );

        $users = array_map(
            function( $user ) {
                return [
                    'email' => $user->data->user_email,
                    'full_name' => $user->data->display_name,
                ];
            },
            $users
        );

        wemail()->api->sync()->subscribers()->wp()->update()->post( [ 'users' => $users ] );
    }

    /**
     * Delete subscribers
     *
     * @param $user_ids
     * @return void
     * @since 1.0.0
     */
    public function delete( $users ) {
        wemail_set_owner_api_key( false );
        $emails = [];

        foreach ( $users as $user ) {
            $emails[] = $user->data->user_email;
        }

        wemail()->api->sync()->subscribers()->wp()->unsubscribe()->post( [ 'emails' => $emails ] );
    }

    /**
     * Create subscribers
     *
     * @param $user_ids
     * @return array
     * @since 1.0.0
     */
    private function filter_syncable_users( $user_ids ) {
        $syncables = [];

        if ( $this->is_active() && ! empty( $this->settings['user_roles'] ) && is_array( $this->settings['user_roles'] ) ) {
            foreach ( $user_ids as $user_id ) {
                $user = get_userdata( $user_id );

                if ( empty( $user->roles ) ) {
                    continue;
                }

                $should_sync = false;

                foreach ( $user->roles as $role ) {
                    if ( in_array( $role, $this->settings['user_roles'], true ) ) {
                        $should_sync = true;
                        break;
                    }
                }

                if ( $should_sync ) {
                    $syncables[] = $user;
                }
            }
        }

        return $syncables;
    }

}
