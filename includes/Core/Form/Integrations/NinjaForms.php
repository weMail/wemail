<?php

namespace WeDevs\WeMail\Core\Form\Integrations;

use WeDevs\WeMail\Core\Form\Integrations\AbstractIntegration;
use WeDevs\WeMail\Traits\Singleton;

class NinjaForms extends AbstractIntegration {

    use Singleton;

    /**
     * Integration title
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $title = 'Ninja Forms';

    /**
     * Integration slug
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $slug = 'ninja_forms';

    /**
     * Execute right after making class instance
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function boot() {
        if ( class_exists( 'Ninja_Forms' ) ) {
            $ninja_forms_version = get_option( 'ninja_forms_version', '0.0.0' );

            if ( version_compare( $ninja_forms_version, '3', '>=' ) ) {
                $this->is_active = true;
            }
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

        $nf = Ninja_Forms();
        $nf_forms = $nf->form()->get_forms();

        foreach ( $nf_forms as $nform ) {
            $form_id = $this->cast_form_id( $nform->get_id() );
            $form_settings = $nform->get_settings();
            $fields = $nf->form( $form_id )->get_fields();

            $form = [
                'id'     => $form_id,
                'title'  => $form_settings['title'],
                'fields' => []
            ];

            foreach ( $fields as $field ) {
                $field_id = $field->get_id();
                $field_settings = $field->get_settings();

                $form['fields'][] = [
                    'id'    => $field_id,
                    'label' => $field_settings['label']
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
     * @param int $sub_id
     *
     * @return void
     */
    public function submit( $sub_id ) {
        $submission = Ninja_Forms()->form()->get_sub( $sub_id );

        $form_id = $this->cast_form_id( $submission->get_form_id() );

        $settings = get_option( 'wemail_form_integration_ninja_forms', [] );

        if ( ! in_array( $form_id, $settings ) ) {
            return;
        }

        $data = [
            'id' => $form_id
        ];

        $field_values = $submission->get_field_values();

        foreach ( $field_values as $key => $value ) {
            preg_match( '/_field_(\d+)/', $key, $matches );

            if ( ! empty( $matches ) && ! empty( $matches[1] ) ) {
                $field_id = intval( $matches[1] );

                $data['data'][ $field_id ] = $value;
            }
        }

        if ( ! empty( $data['data'] ) ) {
            wemail_set_owner_api_key();
            wemail()->api->forms()->integrations( 'ninja-forms' )->submit()->post( $data );
        }
    }

}
