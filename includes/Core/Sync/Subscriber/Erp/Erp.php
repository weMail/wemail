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
                'default_list' => null
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
     * @since 1.0.0
     *
     * @return void
     */
    public function create() {
        if ( ! $this->is_active() ) {
            return;
        }

        /**
         * Non-admin users can register or update their own profile.
         * In that case, we have to use the owner's key to import
         * his/her profile
         */
        wemail_set_owner_api_key( false );

        wemail()->api->sync()->subscribers()->erp()->post();
    }

    /**
     * Update subscribers
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function update( $contact_ids ) {
        if ( ! $this->is_active() ) {
            return;
        }

        wemail_set_owner_api_key( false );

        $contact_ids = array_unique( $contact_ids );

        $contact_ids = implode( ',', $contact_ids );

        wemail()->api->sync()->subscribers()->erp()->put( [ 'ids' => $contact_ids ] );
    }

    /**
     * Delete subscribers
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function delete( $contacts ) {
        wemail_set_owner_api_key( false );

        $hard = [];
        $soft = [];

        foreach ( $contacts as $contact ) {
            if ( ! wemail_validate_boolean( $contact['hard'] ) ) {
                $soft[] = $contact['email'];
            } else {
                $hard[] = $contact['email'];
            }
        }

        $hard = implode( ',', $hard );
        $soft = implode( ',', $soft );

        if ( ! empty( $hard ) ) {
            wemail()->api->sync()->subscribers()->erp()->delete( [ 'ids' => $hard, 'permanent' => true ] );
        }

        if ( ! empty( $soft ) ) {
            wemail()->api->sync()->subscribers()->erp()->delete( [ 'ids' => $soft ] );
        }
    }

    /**
     * Restore subscribers
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function restore( $contacts ) {
        if ( ! $this->is_active() ) {
            return;
        }

        wemail_set_owner_api_key( false );

        $contacts = array_unique( $contacts );

        $contacts = implode( ',', $contacts );

        wemail()->api->sync()->subscribers()->erp()->restore()->put( [ 'ids' => $contacts ] );
    }

}
