<?php

namespace WeDevs\WeMail\Modules\User;

use WeDevs\WeMail\Framework\Traits\Hooker;

class User {

    use Hooker;

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
        $api_key  = get_user_meta( get_current_user_id(), 'wemail_api_key', true );
        $api_key  = apply_filters( 'wemail_api_key', $api_key );

        if ($api_key) {
            $me = wemail()->api->auth()->users()->me()->query( ['include' => 'role,permissions'] )->get();

            if ( !empty( $me['data'] ) ) {
                $this->hash = $me['data']['hash'];
                $this->role = $me['data']['role'];
                $this->permissions = $me['data']['permissions'];
            }
        }

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
