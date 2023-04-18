<?php

namespace WeDevs\WeMail\Core\Form\Integrations;

use WP_Error;
use WeDevs\WeMail\Core\Form\Integrations\AbstractIntegration;
use WeDevs\WeMail\Traits\Singleton;

class Wpforms extends AbstractIntegration {

    use Singleton;

    /**
     * Integration title
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $title = 'WPForms';

    /**
     * Integration slug
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $slug = 'wpforms';

    /**
     * Execute right after making class instance
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function boot() {
        if ( class_exists( 'WPForms' ) ) {
            $this->is_active = true;
        }
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

        $wpforms = wpforms()->form->get();

        if ( ! empty( $wpforms ) ) {
            foreach ( $wpforms as $wpform ) {
                $form = [
                    'id' => $wpform->ID,
                    'title' => $wpform->post_title,
                    'fields' => [],
                ];

                $wpform_fields = wpforms_get_form_fields( $wpform );

                foreach ( $wpform_fields as $wpform_field ) {
                    $form['fields'][] = [
                        'id' => $wpform_field['id'],
                        'label' => $wpform_field['label'],
                    ];
                }

                $forms[] = $form;
            }
        }

        return $forms;
    }

    /**
     * Executes after submit a form
     *
     * @since 1.0.0
     *
     * @param  array $fields
     * @param  array $entry
     * @param  array $form_data
     * @param  int   $entry_id
     *
     * @return void
     */
    public function submit( $fields, $entry, $form_data, $entry_id ) {
        $form_id = $this->cast_form_id( $form_data['id'] );

        $settings = get_option( 'wemail_form_integration_wpforms', [] );

        if ( ! in_array( $form_id, $settings, true ) ) {
            return;
        }

        $data = [
            'id' => $form_id,
        ];

        foreach ( $fields as $field ) {
            $data['data'][ $field['id'] ] = $field['value'];
        }

        if ( ! empty( $data['data'] ) ) {
            wemail_set_owner_api_key();
            wemail()->api->forms()->integrations( 'wpforms' )->submit()->post( $data );
        }
    }

}
