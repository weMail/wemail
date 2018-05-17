<?php

namespace WeDevs\WeMail\Core\Form\Integrations;

use WP_Error;
use WeDevs\WeMail\Core\Form\Integrations\AbstractIntegration;
use WeDevs\WeMail\Traits\Singleton;

class Weforms extends AbstractIntegration {

    use Singleton;

    /**
     * Integration title
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $title = 'weForms';

    /**
     * Integration slug
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $slug = 'weforms';

    /**
     * Execute right after making class instance
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function boot() {
        if ( class_exists( 'WeForms' ) ) {
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

        $weforms_forms = weforms()->form->all();
        $weforms_forms = $weforms_forms['forms'];

        foreach ( $weforms_forms as $weforms_form ) {
            $form_id = $this->cast_form_id( $weforms_form->id );

            $form = [
                'id'     => $form_id,
                'title'  => $weforms_form->name,
                'fields' => [],
            ];

            $weforms_form_fields = $weforms_form->get_fields();

            foreach ( $weforms_form_fields as $weforms_form_fields ) {
                $form['fields'][] = [
                    'id'    => $weforms_form_fields['id'],
                    'label' => $weforms_form_fields['label']
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
     * @return void
     */
    public function submit( $entry_id, $form_id, $page_id, $form_settings ) {
        $form_id = $this->cast_form_id( $form_id );

        $settings = get_option( 'wemail_form_integration_weforms', [] );

        if ( ! in_array( $form_id, $settings ) ) {
            return;
        }

        $data = [
            'id' => $form_id
        ];

        $weforms_form = weforms()->form->get( $form_id );

        $weforms_entries = new \WeForms_Form_Entry( $entry_id, $weforms_form );
        $weforms_entries = $weforms_entries->get_fields();

        foreach ( $weforms_entries as $weforms_entry ) {
            if ( ! empty( $weforms_entry['id'] ) && ! empty( $weforms_entry['value'] ) ) {
                $data['data'][ $weforms_entry['id'] ] = $weforms_entry['value'];
            }
        }

        if ( ! empty( $data['data'] ) ) {
            wemail_set_owner_api_key();
            wemail()->api->forms()->integrations( 'weforms' )->submit()->post( $data );
        }
    }

}
