<?php
namespace WeDevs\WeMail;

use WeDevs\WeMail\Core\Form\Integrations\Hooks as FormIntegrations;
use WeDevs\WeMail\Traits\Hooker;

class Hooks {

    use Hooker;

    public function __construct() {
        $this->add_action( 'user_register', 'update_wp_subscriber' );
        $this->add_action( 'profile_update', 'update_wp_subscriber' );
        $this->add_action( 'delete_user', 'delete_wp_subscriber' );

        new FormIntegrations();
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
        wemail()->subscriber->update_wp_subscriber( $user_id );
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
        wemail()->subscriber->delete_wp_subscriber( $user_id );
    }

}
