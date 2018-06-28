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
                'user_roles' => []
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
            $user_ids = $this->filter_syncable_users( $user_ids );

            if ( empty( $user_ids ) ) {
                return;
            }
        }

        /**
         * Non-admin users can register or update their own profile.
         * In that case, we have to use the owner's key to import
         * his/her profile
         */
        wemail_set_owner_api_key( false );

        wemail()->api->sync()->subscribers()->wp()->post();
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

        $user_ids = $this->filter_syncable_users( $user_ids );

        if ( empty( $user_ids ) ) {
            return;
        }

        wemail_set_owner_api_key( false );

        $user_ids = implode( ',', $user_ids );

        wemail()->api->sync()->subscribers()->wp()->put( [ 'ids' => $user_ids ] );
    }

    /**
     * Delete subscribers
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function delete( $user_ids ) {
        wemail_set_owner_api_key( false );

        $user_ids = implode( ',', $user_ids );

        wemail()->api->sync()->subscribers()->wp()->delete( [ 'ids' => $user_ids ] );
    }

    /**
     * Create subscribers
     *
     * @since 1.0.0
     *
     * @return void
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
                    if ( in_array( $role, $this->settings['user_roles'] ) ) {
                        $should_sync = true;
                        break;
                    }
                }

                if ( $should_sync ) {
                    $syncables[] = $user_id;
                }
            }
        }

        return $syncables;
    }

}
