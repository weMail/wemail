<?php

namespace WeDevs\WeMail\Core\Sync\Subscriber\Wp;

use WeDevs\WeMail\Traits\Hooker;

class Hooks {

    use Hooker;

    /**
     * Holds the newly created user ids
     *
     * @since 1.0.0
     *
     * @var array
     */
    private $created_users = array();

    /**
     * Holds the updated user ids
     *
     * @since 1.0.0
     *
     * @var array
     */
    private $updated_users = array();

    /**
     * Holds the deleted user ids
     *
     * @since 1.0.0
     *
     * @var array
     */
    private $deleted_users = array();

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->add_action( 'user_register', 'create_subscriber_on_shutdown' );
        $this->add_action( 'profile_update', 'update_subscriber_on_shutdown' );
        $this->add_action( 'delete_user', 'delete_subscriber_on_shutdown' );
    }

    /**
     * Add create method in shutdown hook
     *
     * @since 1.0.0
     *
     * @param int $user_id
     *
     * @return void
     */
    public function create_subscriber_on_shutdown( $user_id ) {
        $this->created_users[] = $user_id;
        $this->add_shutdown_action( 'create' );
    }

    /**
     * Add update method in shutdown hook
     *
     * @since 1.0.0
     *
     * @param int $user_id
     *
     * @return void
     */
    public function update_subscriber_on_shutdown( $user_id ) {
        $this->updated_users[] = $user_id;
        $this->add_shutdown_action( 'update' );
    }

    /**
     * Add delete method in shutdown hook
     *
     * @since 1.0.0
     *
     * @param int $user_id
     *
     * @return void
     */
    public function delete_subscriber_on_shutdown( $user_id ) {
        $this->deleted_users[] = get_userdata( $user_id );
        $this->add_shutdown_action( 'delete' );
    }

    /**
     * Sync newly created users
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function create() {
        wemail()->sync->subscriber->wp->create( $this->created_users );
    }

    /**
     * Sync updated users
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function update() {
        wemail()->sync->subscriber->wp->update( $this->updated_users );
    }

    /**
     * Sync deleted users
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function delete() {
        wemail()->sync->subscriber->wp->delete( $this->deleted_users );
    }

    /**
     * Add shutdown hook
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function add_shutdown_action( $name ) {
        if ( ! has_action( 'shutdown', array( $this, $name ) ) ) {
            $this->add_action( 'shutdown', $name );
        }
    }
}
