<?php

namespace WeDevs\WeMail\Core\Sync\Subscriber\Wp;

use WeDevs\WeMail\Traits\Hooker;

class Hooks {

    use Hooker;

    private $created_users = [];

    private $updated_users = [];

    private $deleted_users = [];

    public function __construct() {
        $this->add_action( 'wemail_saved_settings_sync_subscriber_wp', 'create' );
        $this->add_action( 'user_register', 'create_subscriber_on_shutdown' );
        $this->add_action( 'profile_update', 'update_subscriber_on_shutdown' );
        $this->add_action( 'delete_user', 'delete_subscriber_on_shutdown' );
    }

    public function create_subscriber_on_shutdown( $user_id ) {
        $this->created_users[] = $user_id;
        $this->add_shutdown_action( 'create' );
    }

    public function update_subscriber_on_shutdown( $user_id ) {
        $this->updated_users[] = $user_id;
        $this->add_shutdown_action( 'update' );
    }

    public function delete_subscriber_on_shutdown( $user_id ) {
        $this->deleted_users[] = $user_id;
        $this->add_shutdown_action( 'delete' );
    }

    public function create() {
        wemail()->sync->subscriber->wp->create( $this->created_users );
    }

    public function update() {
        wemail()->sync->subscriber->wp->update( $this->updated_users );
    }

    public function delete() {
        wemail()->sync->subscriber->wp->delete( $this->deleted_users );
    }

    private function add_shutdown_action( $name ) {
        if ( ! has_action( 'shutdown', [ $this, $name ] ) ) {
            $this->add_action( 'shutdown', $name );
        }
    }
}
