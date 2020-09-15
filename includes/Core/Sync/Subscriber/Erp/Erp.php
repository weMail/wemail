<?php

namespace WeDevs\WeMail\Core\Sync\Subscriber\Erp;

class Erp {

    /**
     * Sync settings
     *
     * @var array
     *
     * @since 1.0.0
     */
    private $settings;

    /**
     * Sync settings
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function settings() {
        if ( ! isset( $this->settings['sync'] ) ) {
            $defaults = [
                'sync' => false,
                'import_crm_groups' => false,
                'default_list' => null,
            ];

            $settings = get_option( 'wemail_sync_subscriber_erp_contacts', [] );

            if ( ! empty( $settings ) ) {
                if ( ! empty( $data['sync'] ) ) {
                    $settings['sync'] = wemail_validate_boolean( $data['sync'] );
                }

                if ( ! empty( $data['import_crm_groups'] ) ) {
                    $settings['import_crm_groups'] = wemail_validate_boolean( $data['import_crm_groups'] );
                }
            }

            $this->settings = wp_parse_args( $settings, $defaults );
        }

        return $this->settings;
    }

    /**
     * Checks if syncing enabled
     *
     * @since 1.0.0
     *
     * @return bool
     */
    public function is_active() {
        if ( ! is_erp_crm_active() ) {
            return false;
        }

        $settings = $this->settings();

        if ( ! empty( $settings['sync'] ) && wemail_validate_boolean( $settings['sync'] ) ) {
            return true;
        }

        return false;
    }

    /**
     * Create subscribers
     *
     * @param array $contacts
     *
     * @return void
     * @since 1.0.0
     */
    public function create( $contacts = [] ) {
        if ( ! $this->is_active() ) {
            return;
        }

        $contacts = $this->format_contacts( $contacts );
        /**
         * Non-admin users can register or update their own profile.
         * In that case, we have to use the owner's key to import
         * his/her profile
         */
        wemail_set_owner_api_key( false );

        wemail()->api->sync()->subscribers()->erp()->subscribed()->post(
            [
                'list_id' => $this->settings['default_list'],
                'contacts' => $contacts,
            ]
        );
    }

    /**
     * Update subscribers
     *
     * @param $contacts
     *
     * @return void
     * @since 1.0.0
     */
    public function update( $contacts ) {
        if ( ! $this->is_active() ) {
            return;
        }

        $contacts = $this->format_contacts( $contacts );

        wemail_set_owner_api_key( false );

        wemail()->api->sync()->subscribers()->erp()->subscribed()->post(
            [
                'list_id' => $this->settings['default_list'],
                'contacts' => $contacts,
            ]
        );
    }

    /**
     * Delete subscribers
     *
     * @param $contacts
     *
     * @return void
     * @since 1.0.0
     */
    public function delete( $contacts ) {
        if ( ! $this->is_active() ) {
            return;
        }

        wemail_set_owner_api_key( false );

        if ( empty( $contacts ) ) {
            return;
        }

        $emails = [];

        foreach ( $contacts as $contact ) {
            $emails[] = $contact->email;
        }

        wemail()->api->sync()->subscribers()->erp()->unsubscribed()->post(
            [
                'list_id' => $this->settings['default_list'],
                'emails' => $emails,
            ]
        );
    }

    public function sync_group( $contact_ids = [] ) {
        if ( ! $this->is_active() ) {
            return;
        }

        if ( ! $this->settings['import_crm_groups'] ) {
            return;
        }

        wemail_set_owner_api_key( false );

        wemail()->api->sync()->subscribers()->erp()->sync_group()->post(
            [
                'list_id' => $this->settings['default_list'],
                'contact_ids' => $contact_ids,
            ]
        );
    }

    /**
     * Format contact
     *
     * @param array $contacts [description]
     *
     * @return array|array[] [type]           [description]
     */
    protected function format_contacts( array $contacts ) {
        return array_map(
            function ( $contact ) {
                return array_merge(
                    wemail_array_only(
                        $contact,
                        [
                            'first_name',
                            'last_name',
                            'email',
                            'phone',
                            'mobile',
                            'city',
                            'state',
                            'country',
                            'date_of_birth',
                        ]
                    ),
                    [ 'source' => 'erp' ]
                );
            },
            $contacts
        );
    }
}
