<?php

namespace WeDevs\WeMail\Core\Sync\Subscriber\Erp;

use WeDevs\WeMail\Traits\Hooker;

class Hooks {

    use Hooker;

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
        $this->add_action( 'wemail_saved_settings_sync_subscriber_erp_contacts', 'create' );
        $this->add_action( 'erp_create_new_people', 'create_subscriber_on_shutdown', 10, 3 );
        $this->add_action( 'erp_update_people', 'update_subscriber_on_shutdown', 10, 3 );
        $this->add_action( 'erp_crm_create_contact_subscriber', 'update_subscriber_group_on_shutdown' );
        $this->add_action( 'erp_crm_edit_contact_subscriber', 'update_subscriber_group_on_shutdown' );
        $this->add_action( 'erp_crm_delete_contact_subscriber', 'update_subscriber_group_on_shutdown' );

        $this->add_action( 'erp_before_delete_people', 'before_delete_subscriber', 10, 2 );
        $this->add_action( 'erp_after_delete_people', 'delete_subscriber_on_shutdown', 10, 2 );

        $this->add_action( 'erp_after_restoring_people', 'restore_subscriber_on_shutdown', 10, 2 );
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
    public function create_subscriber_on_shutdown( $people_id, $args, $type ) {
        if ( $type === 'contact' ) {
            $this->add_shutdown_action( 'create' );
        }
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
    public function update_subscriber_on_shutdown( $people_id, $args, $type ) {
        if ( $type === 'contact' ) {
            $this->updated_contacts[] = $people_id;
            $this->add_shutdown_action( 'update' );
        }
    }

    /**
     * Add update method in shutdown hook when update the contact group
     *
     * @since 1.0.0
     *
     * @param int $user_id
     *
     * @return void
     */
    public function update_subscriber_group_on_shutdown( $subscriber ) {
        if ( ! has_action( 'shutdown', [ $this, 'create' ] ) && ! has_action( 'shutdown', [ $this, 'update' ] ) ) {
            $this->updated_contacts[] = $subscriber->user_id;
            $this->add_shutdown_action( 'update' );
        }
    }

    /**
     * erp_before_delete_people hook
     *
     * @since 1.0.0
     *
     * @param int   $user_id
     * @param array $data
     *
     * @return void
     */
    public function before_delete_subscriber( $people_id, $data ) {
        if ( ! empty( $data['type'] ) && $data['type'] === 'contact' ) {
            $contact = \WeDevs\ERP\Framework\Models\People::find( $people_id );

            if ( $contact && $contact->email ) {
                $this->deleting_contacts[ $contact->id ] = [
                    'email' => $contact->email,
                    'hard' => $data['hard']
                ];
            }
        }
    }

    /**
     * Add delete method in shutdown hook
     *
     * @since 1.0.0
     *
     * @param int   $user_id
     * @param array $data
     *
     * @return void
     */
    public function delete_subscriber_on_shutdown( $people_id, $data ) {
        if ( isset( $this->deleting_contacts[ $people_id ] ) ) {
            $this->deleted_contacts[] = $this->deleting_contacts[ $people_id ];

            $this->add_shutdown_action( 'delete' );
        }
    }

    /**
     * Add restore method in shutdown hook
     *
     * @since 1.0.0
     *
     * @param int   $user_id
     * @param array $data
     *
     * @return void
     */
    public function restore_subscriber_on_shutdown( $people_id, $data ) {
        if ( ! empty( $data['type'] ) && $data['type'] === 'contact' ) {
            $contact = \WeDevs\ERP\Framework\Models\People::find( $people_id );

            if ( $contact && $contact->email ) {
                $this->restored_contacts[] = $contact->email;
                $this->add_shutdown_action( 'restore' );
            }
        }
    }

    /**
     * Sync newly created users
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function create() {
        wemail()->sync->subscriber->erp->create();
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
     * Sync restore users
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function restore() {
        wemail()->sync->subscriber->erp->restore( $this->restored_contacts );
    }

    /**
     * Add shutdown hook
     *
     * @since 1.0.0
     *
     * @return void
     */
    private function add_shutdown_action( $name ) {
        if ( ! has_action( 'shutdown', [ $this, $name ] ) ) {
            $this->add_action( 'shutdown', $name );
        }
    }
}
