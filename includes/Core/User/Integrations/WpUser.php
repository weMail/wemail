<?php

namespace WeDevs\WeMail\Core\User\Integrations;

class WpUser {
    /**
     * Class constructor
     * since 1.0.0
     */
    public function __construct()
    {
        add_action( 'user_register', [$this, 'wemail_registration_save'], 10, 1);
        add_action( 'profile_update', [$this, 'wemail_user_profile_updated'], 10, 2);
        add_action( 'delete_user', [$this, 'wemail_user_deleted'] );
    }

    /**
     * Set Access key while registered if
     * user is administrator / editor
     * @param  [int] $user_id [id of registered user]
     * @return void
     */
    public function wemail_registration_save( $user_id ) {
        $user = get_userdata( $user_id );
        $data = [
            'name' => $user->data->display_name,
            'email' => $user->data->user_email,
            'role' => 'admin'
        ];

        $userId = get_current_user_id();
        if (!$userId) {
           return;
        }
        $access_token = get_user_meta( $userId, 'wemail_api_key', false );
        if ($access_token) {
            wemail()->api->set_api_key( $access_token[0] );
            $roles = $user->roles;
            if (count($roles) > 0 && in_array('administrator', array_values($roles))) {
                $wp_admin_response = wemail()->api->auth()->users()->post( $data );
                if ( isset($wp_admin_response['access_token']) && $wp_admin_response['access_token'] !== '' ) {
                    update_user_meta( $user_id, 'wemail_api_key', $wp_admin_response['access_token'] );
                }
            }

            if (count($roles) > 0 && !in_array('administrator', array_values($roles)) && in_array('editor', array_values($roles))) {
                $data['role'] = 'team';
                $wp_admin_response = wemail()->api->auth()->users()->post( $data );
                if ( isset($wp_admin_response['access_token']) && $wp_admin_response['access_token'] !== '' ) {
                    update_user_meta( $user_id, 'wemail_api_key', $wp_admin_response['access_token'] );
                }
            }
        }
    }

    public function wemail_user_profile_updated( $user_id, $old_user_data ) {
        $user = get_userdata( $user_id );
        $data = [
            'name' => $user->data->display_name,
            'email' => $user->data->user_email,
        ];

        $userId = get_current_user_id();
        if (!$userId) {
           return;
        }
        $access_token = get_user_meta( $userId, 'wemail_api_key', false );
        $user_token   = get_user_meta( $user_id, 'wemail_api_key', false );
        if ($user_token && $access_token) {
            wemail()->api->set_api_key( $access_token[0] );
            $data['token'] = $user_token[0];
            $wp_admin_response = wemail()->api->auth()->users()->profile()->update()->post( $data );
            if ($wp_admin_response['success']) {
                delete_user_meta( $user_id, 'wemail_user_data' );
            }
        }
    }

    public function wemail_user_deleted($user_id)
    {
        $userId = get_current_user_id();
        if (!$userId) {
           return;
        }
        $access_token = get_user_meta( $userId, 'wemail_api_key', false );
        if ($access_token) {
            wemail()->api->set_api_key( $access_token[0] );
            $user = get_userdata( $user_id );
            if ($user) {
                if ( ! get_user_meta( $user->ID, 'wemail_api_key', true ) ) {
                    return;
                }

                $wp_admin_response = wemail()->api->auth()->users()->destroy()->post([
                    'email'  => $user->data->user_email
                ]);

                if ( is_wp_error( $wp_admin_response ) ) {
                    return;
                }

                if ($wp_admin_response['success']) {
                    delete_user_meta( $user->ID, 'wemail_api_key' );
                    delete_user_meta( $user->ID, 'wemail_user_data' );
                    delete_user_meta( $user->ID, 'wemail_disable_user_access' );
                }
            }
        }
    }
}
