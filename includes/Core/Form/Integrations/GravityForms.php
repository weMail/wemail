<?php

namespace WeDevs\WeMail\Core\Form\Integrations;

use WeDevs\WeMail\Core\Form\Integrations\AbstractIntegration;
use WeDevs\WeMail\Traits\Singleton;

class GravityForms extends AbstractIntegration {

    use Singleton;

    /**
     * Integration title
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $title = 'Gravity Forms';

    /**
     * Integration slug
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $slug = 'gravity_forms';

    /**
     * Execute right after making class instance
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function boot() {
        if ( class_exists( 'GFForms' ) ) {
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

        $gf_forms = \GFFormsModel::get_forms( true );

        foreach ( $gf_forms as $gf_form ) {
            $form_id = $this->cast_form_id( $gf_form->id );
            $form = [
                'id'     => $form_id,
                'title'  => $gf_form->title,
                'fields' => [],
            ];

            $form_meta = \GFFormsModel::get_form_meta( $form_id );

            foreach ( $form_meta['fields'] as $field ) {
                $field = \GF_Fields::create( $field );

                if ( empty( $field['inputs'] ) ) {
                    $form['fields'][] = [
                        'id'    => $field->id,
                        'label' => $field->label
                    ];

                } else {
                    foreach ( $field['inputs'] as $i => $group_field) {
                        if ( empty( $group_field['isHidden'] ) ) {
                            $form['fields'][] = [
                                'id'    => $group_field['id'],
                                'label' => $group_field['label']
                            ];
                        }
                    }
                }
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
     * @param  array $lead
     * @param  array $form
     *
     * @return void
     */
    public function submit( $lead, $form ) {
        $form_id = $lead['form_id'];

        $settings = get_option( 'wemail_form_integration_gravity_forms', [] );

        if ( ! in_array( $form_id, $settings ) ) {
            return;
        }

        $data = [
            'id' => $form_id
        ];

        foreach ( $form['fields'] as $field ) {
            $field = \GF_Fields::create( $field );

            if ( empty( $field['inputs'] ) ) {
                $data['data'][ $field->id ] = $lead[ $field->id ];

            } else {
                foreach ( $field['inputs'] as $group_field ) {
                    if ( empty( $group_field['isHidden'] ) ) {
                        $data['data'][ $group_field['id'] ] = $lead[ $group_field['id'] ];
                    }
                }
            }
        }

        if ( ! empty( $data['data'] ) ) {
            wemail_set_owner_api_key();
            wemail()->api->forms()->integrations( 'gravity-forms' )->submit()->post( $data );
        }
    }

}
