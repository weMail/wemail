<?php

namespace WeDevs\WeMail\Core\User\Integrations;

class WpUser {

    /**
     * Class constructor
     * since 1.0.0
     */
    public function __construct() {
        add_action( 'user_register', array( $this, 'wemail_registration_save' ), 10, 1 );
        add_action( 'profile_update', array( $this, 'wemail_user_profile_updated' ), 10, 2 );
        add_action( 'delete_user', array( $this, 'wemail_user_deleted' ) );
    }

    /**
     * Handle create new user action
     *
     * @param $user_id
     */
    public function wemail_registration_save( $user_id ) {
        $user = get_userdata( $user_id );

        // If user are not found!
        if ( ! $user ) {
            return;
        }

        if ( $this->is_administrator( $user->roles ) ) {
            $this->create_wemail_user( $user, 'admin' );
        } elseif ( $this->is_team( $user->roles ) ) {
            $this->create_wemail_user( $user, 'team' );
        }
    }

    /**
     * Handle update user action
     *
     * @param $user_id
     * @param $old_user_data
     */
    public function wemail_user_profile_updated( $user_id, $old_user_data ) {
        $user = get_userdata( $user_id );

        if ( ! $user ) {
            return;
        }

        $access_token = get_option( 'wemail_api_key' );

        if ( empty( $access_token ) ) {
            return;
        }

        $data = array(
            'name' => $user->data->display_name,
            'email' => $user->data->user_email,
        );

        $response = wemail()->api->set_api_key( $access_token )->auth()->users()->profile()->update()->post( $data );

        if ( is_wp_error( $response ) ) {
            return;
        }

        // $this->update_user_permission( $access_token, $user_id );
    }

    /**
     * Handle delete user action
     *
     * @param $user_id
     */
    public function wemail_user_deleted( $user_id ) {
        $user = get_userdata( $user_id );

        if ( ! $user ) {
            return;
        }

        $access_token = get_user_meta( $user_id, 'wemail_api_key', true );

        if ( ! $access_token ) {
            return;
        }

        $data = array(
            'email'  => $user->data->user_email,
        );

        $response = wemail()->api->set_api_key( $access_token )->auth()->users()->destroy()->post( $data );

        if ( is_wp_error( $response ) ) {
            return;
        }

        if ( $response['success'] ) {
            delete_user_meta( $user->ID, 'wemail_api_key' );
            delete_user_meta( $user->ID, 'wemail_user_data' );
            delete_user_meta( $user->ID, 'wemail_disable_user_access' );
        }
    }

    /**
     * Check is administrator
     *
     * @param $roles
     *
     * @return bool
     */
    protected function is_administrator( $roles ) {
        if ( empty( $roles ) ) {
            return false;
        }

        return in_array( 'administrator', $roles, true );
    }

    /**
     * Check is user under team roles
     *
     * @param $roles
     *
     * @return bool
     */
    protected function is_team( $roles ) {
        if ( empty( $roles ) ) {
            return false;
        }

        $roles = array_intersect( $roles, get_option( 'wemail_accessible_roles', array( 'administrator', 'editor' ) ) );

        return ! empty( $roles );
    }

    /**
     * Check role
     *
     * @param $roles
     * @param $role
     *
     * @return bool
     */
    protected function check_role( $roles, $role ) {
        if ( empty( $roles ) ) {
            return false;
        }

        return in_array( $role, $roles, true );
    }

    /**
     * Create user on weMail server
     *
     * @param \WP_User|false $user
     * @param string $role
     */
    protected function create_wemail_user( $user, $role ) {
        $response = wemail()->api->teamUsers()->post(
            array(
                'name' => $user->data->display_name,
                'email' => $user->data->user_email,
                'role' => $role,
            )
        );

        if ( is_wp_error( $response ) ) {
            return;
        }

        if ( ! isset( $response['access_token'] ) ) {
            return;
        }

        // update_user_meta( $user->ID, 'wemail_api_key', $response['access_token'] );

        // $this->update_user_permission( $response['access_token'], $user->ID );
    }

    /**
     * Update user permissions on WP Database
     *
     * @param $access_token
     * @param $user_id
     */
    protected function update_user_permission( $access_token, $user_id ) {
        // $api_key  = apply_filters( 'wemail_api_key', $access_token );
        // $user_data = wemail()->api->set_api_key( $api_key )->auth()->users()->me()->query( array( 'include' => 'role,permissions' ) )->get();

        // if ( is_wp_error( $user_data ) ) {
        //     return;
        // }

        // if ( ! empty( $user_data['data'] ) ) {
        //     $user_data = $user_data['data'];

        //     update_user_meta( $user_id, 'wemail_user_data', $user_data );
        // }
    }
}
