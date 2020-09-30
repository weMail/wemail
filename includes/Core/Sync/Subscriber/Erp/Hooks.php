<?php

namespace WeDevs\WeMail\Core\Sync\Subscriber\Erp;

use WeDevs\WeMail\Traits\Hooker;

class Hooks {

    use Hooker;

    /**
     * @since 1.0.6
     * @var array
     */
    private $created_contacts = [];

    /**
     * Holds the updated contact ids
     *
     * @since 1.0.0
     *
     * @var array
     */
    private $updated_contacts = [];

    /**
     * Holds the deleting contacts
     *
     * @since 1.0.0
     *
     * @var array
     */
    private $deleting_contacts = [];

    /**
     * Holds the deleted contacts
     *
     * @since 1.0.0
     *
     * @var array
     */
    private $deleted_contacts = [];

    /**
     * Holds the restored contact ids
     *
     * @var array
     */
    private $restored_contacts = [];

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->add_action( 'erp_create_new_people', 'create_subscriber_on_shutdown', 10, 3 );
        $this->add_action( 'erp_update_people', 'update_subscriber_on_shutdown', 10, 3 );

        $this->add_action( 'erp_crm_create_contact_subscriber', 'update_subscriber_group_on_shutdown' );
        $this->add_action( 'erp_crm_edit_contact_subscriber', 'update_subscriber_group_on_shutdown' );
        $this->add_action( 'erp_crm_delete_contact_subscriber', 'update_subscriber_group_on_shutdown' );

        $this->add_action( 'erp_before_delete_people', 'before_delete_subscriber', 10, 2 );
        $this->add_action( 'erp_after_delete_people', 'delete_subscriber_on_shutdown', 10, 2 );
    }

    /**
     * Add create method in shutdown hook
     *
     * @param $people_id
     * @param $args
     * @param $type
     *
     * @return void
     * @since 1.0.0
     */
    public function create_subscriber_on_shutdown( $people_id, $args, $type ) {
        if ( $type === 'contact' ) {
            $this->created_contacts[] = $args;
            $this->add_shutdown_action( 'create' );
        }
    }

    /**
     * Add update method in shutdown hook
     *
     * @param $people_id
     * @param $args
     * @param $type
     *
     * @return void
     * @since 1.0.0
     */
    public function update_subscriber_on_shutdown( $people_id, $args, $type ) {
        if ( $type === 'contact' ) {
            $this->updated_contacts[] = $args;
            $this->add_shutdown_action( 'update' );
        }
    }

    /**
     * Add update method in shutdown hook when update the contact group
     *
     * @param $subscriber
     *
     * @return void
     * @since 1.0.0
     */
    public function update_subscriber_group_on_shutdown( $subscriber ) {
        if ( ! has_action( 'shutdown', [ $this, 'create' ] ) && ! has_action( 'shutdown', [ $this, 'update' ] ) ) {
            $this->updated_contacts[] = $subscriber->user_id;

            $this->add_shutdown_action( 'sync_group' );
        }
    }

    /**
     * Erp_before_delete_people hook
     *
     * @param $people_id
     * @param array $data
     *
     * @return void
     * @since 1.0.0
     */
    public function before_delete_subscriber( $people_id, $data ) {
        if ( ! empty( $data['type'] ) && $data['type'] === 'contact' ) {
            if ( $data['hard'] !== 1 ) {
                return;
            }

            $contact = \WeDevs\ERP\Framework\Models\People::find( $people_id );

            if ( $contact && $contact->email ) {
                $this->deleting_contacts[ $contact->id ] = $contact;
            }
        }
    }

    /**
     * Add delete method in shutdown hook
     *
     * @param $people_id
     * @param array $data
     *
     * @return void
     * @since 1.0.0
     */
    public function delete_subscriber_on_shutdown( $people_id, $data ) {
        if ( ( ! empty( $data['type'] ) && $data['type'] !== 'contact' ) || $data['hard'] !== 1 ) {
            return;
        }
        foreach ( $this->deleting_contacts as $people_id => $contact ) {
            if ( isset( $this->deleting_contacts[ $people_id ] ) ) {
                $this->deleted_contacts[] = $contact;
            }
        }
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
        wemail()->sync->subscriber->erp->create( $this->created_contacts );
    }

    /**
     * Sync updated users
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function update() {
        wemail()->sync->subscriber->erp->update( $this->updated_contacts );
    }

    /**
     * Sync contact group
     */
    public function sync_group() {
        wemail()->sync->subscriber->erp->sync_group( $this->updated_contacts );
    }

    /**
     * Sync deleted users
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function delete() {
        wemail()->sync->subscriber->erp->delete( $this->deleted_contacts );
    }

    /**
     * Add shutdown hook
     *
     * @param $name
     *
     * @return void
     * @since 1.0.0
     */
    private function add_shutdown_action( $name ) {
        if ( ! has_action( 'shutdown', [ $this, $name ] ) ) {
            $this->add_action( 'shutdown', $name );
        }
    }
}
