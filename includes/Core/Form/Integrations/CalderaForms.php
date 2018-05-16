<?php

namespace WeDevs\WeMail\Core\Form\Integrations;

use WP_Error;
use WeDevs\WeMail\Core\Form\Integrations\AbstractIntegration;
use WeDevs\WeMail\Traits\Singleton;

class CalderaForms extends AbstractIntegration {

    use Singleton;

    /**
     * Integration title
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $title = 'Caldera Forms';

    /**
     * Integration slug
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $slug = 'caldera_forms';

    /**
     * Execute right after making class instance
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function boot() {
        if ( class_exists( 'Caldera_Forms' ) ) {
            $this->is_active = true;
        }
    }

    /**
     * Cast the form id
     *
     * @since 1.0.0
     *
     * @param  mixed $form_id
     *
     * @return int
     */
    public function cast_form_id( $form_id ) {
        return $form_id;
    }

    /**
     * Get available forms
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function forms() {
        $forms = [];

        $caldera_forms = \Caldera_Forms_Forms::get_forms( true );

        foreach ( $caldera_forms as $form_id => $caldera_form ) {
            $form_details = \Caldera_Forms_Forms::get_form( $caldera_form['ID'] );

            $form = [
                'id'     => $this->cast_form_id( $caldera_form['ID'] ),
                'title'  => $caldera_form['name'],
                'fields' => [],
            ];

            foreach ( $form_details['fields'] as $caldera_form_field_id => $caldera_form_field ) {
                $form['fields'][] = [
                    'id'    => $caldera_form_field_id,
                    'label' => $caldera_form_field['label']
                ];
            }

            $forms[] = $form;
        }

        return $forms;
    }

    /**
     * Executes after submit a form
     *
     * @since 1.0.0
     *
     * @param array     $form       Form config
     * @param array     $referrer   URL form was submitted via -- is passed through parse_url() before this point.
     * @param string    $process_id Unique ID for this processing
     * @param int|false $entryid    Current entry ID or false if not set or being saved.
     *
     * @return void
     */
    public function submit( $form, $referrer, $process_id, $entryid ) {
        global $processed_data;

        $form_id = $form['ID'];

        $settings = get_option( 'wemail_form_integration_caldera_forms', [] );

        if ( ! in_array( $form_id, $settings ) ) {
            return;
        }

        $data = [
            'id' => $form_id
        ];

        $posted_data = $processed_data[ $form_id ];

        foreach ( $form['fields'] as $field_id => $field ) {
            $data['data'][ $field_id ] = $posted_data[ $field_id ];
        }

        if ( ! empty( $data['data'] ) ) {
            wemail_set_owner_api_key();
            wemail()->api->forms()->integrations( 'caldera-forms' )->submit()->post( $data );
        }
    }

}
