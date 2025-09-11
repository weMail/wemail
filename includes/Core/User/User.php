<?php

namespace WeDevs\WeMail\Core\User;

use WeDevs\WeMail\Traits\Hooker;
use WeDevs\WeMail\Traits\Singleton;

class User {

    use Singleton;
    use Hooker;

    /**
     * WP User ID
     *
     * @var integer
     */
    public $user_id = 0;

    /**
     * User API key
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $api_key = null;

    /**
     * User hash id
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $hash = null;

    /**
     * User role
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $role = null;

    /**
     * Is user allowed to access weMail
     *
     * @since 1.14.16
     *
     * @var boolean
     */
    public $allowed = false;

    /**
     * API resource query for URL to build
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function boot() {
        $user_id = get_current_user_id();
        $user_email = get_user_by( 'id', $user_id )->user_email;

        $disabled = get_user_meta( $user_id, 'wemail_disable_user_access' );
        if ( $disabled ) {
            return;
        }

		//        $api_key  = get_user_meta( $user_id, 'wemail_api_key', true );
        $api_key  = get_option( 'wemail_api_key' );
        $api_key  = apply_filters( 'wemail_api_key', $api_key, $user_id );

        if ( $api_key ) {
            $this->hash = $api_key ? true : false;
            $this->role = wp_get_current_user()->roles;
            $this->allowed = $this->check_user_role( $user_id );
        }

        $this->user_id = $user_id;
        $this->api_key  = $api_key;
    }

    /**
     * Whether current user has a specific permission
     *
     * @since 1.0.0
     *
     * @param string $permission
     *
     * @return bool
     */
    public function can( $permission ) {
        $wemail_api_key = get_option( 'wemail_api_key' );
        if ( ! $wemail_api_key ) {
            return false;
        }

        return $this->check_user_role( $this->user_id );
    }

    public function check_user_role( $user_id ) {
        $accessible_roles = get_option( 'wemail_accessible_roles', array() );
        $current_roles = wp_get_current_user()->roles;

        // Check if user has any accessible role
        $has_accessible_role = ! empty( array_intersect( $current_roles, $accessible_roles ) );

        if ( $has_accessible_role ) {
            // User has accessible role - keep their data
            return true;
        } else {
            // User doesn't have accessible role - delete their data
            if ( get_user_meta( $user_id, 'wemail_user_data', true ) ) {
                delete_user_meta( $user_id, 'wemail_user_data' );
            }
            return false;
        }
    }
}
