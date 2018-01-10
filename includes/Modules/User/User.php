<?php

namespace WeDevs\WeMail\Modules\User;

use WeDevs\WeMail\Framework\Traits\Hooker;

class User {

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
     * User permissions
     *
     * @since 1.0.0
     *
     * @var array
     */
    public $permissions = [];

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->add_action( 'wemail_init', 'boot' );
    }

    /**
     * Execute after wemail is fully loaded
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function boot() {
        $user_id = get_current_user_id();

        $api_key  = get_user_meta( $user_id, 'wemail_api_key', true );
        $api_key  = apply_filters( 'wemail_api_key', $api_key, $user_id );

        if ( $api_key ) {
            $user_data = get_user_meta( $user_id, 'wemail_user_data', true );

            if ( ! $user_data ) {
                $user_data = wemail()->api->auth()->users()->me()->query( ['include' => 'role,permissions'] )->get();

                if ( ! empty( $user_data['data'] ) ) {
                    $user_data = $user_data['data'];

                    update_user_meta( $user_id, 'wemail_user_data', $user_data );
                }
            }


            $this->hash = $user_data['hash'];
            $this->role = $user_data['role'];
            $this->permissions = $user_data['permissions'];
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
        if ( array_key_exists( $permission, $this->permissions ) ) {
            return true;
        }

        return false;
    }

}
