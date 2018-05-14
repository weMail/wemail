<?php

namespace WeDevs\WeMail\Core\Form\Integrations;

use WP_Error;
use WeDevs\WeMail\Traits\Singleton;

class GravityForms {

    use Singleton;

    /**
     * Hold the value if plugin active or not
     *
     * @since 1.0.0
     *
     * @var bool
     */
    public $is_active = false;

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
     * Inactivity message
     *
     * @since 1.0.0
     *
     * @return \WP_Error
     */
    public function inactivity_message() {
        return new WP_Error(
            'integration_is_not_active',
            __('Gravity Forms plugin is not active', 'wemail'),
            ['status' => 422]
        );
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
            $form_id = absint( $gf_form->id );
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
     * Save settings
     *
     * @since 1.0.0
     *
     * @param  array $data
     *
     * @return array|\WP_Error
     */
    public function save( $data ) {
        $data = ! empty( $data ) ? $data : [];

        $settings = [];
        $form_ids = [];

        foreach ( $data as $form ) {
            if ( ! isset( $form['id'] ) || empty( $form['map'] ) ) {
                continue;
            }

            if ( empty( $form['list_id'] ) || ! isset( $form['overwrite'] ) ) {
                continue;
            }

            $form_id = absint( $form['id'] );

            $settings[] = [
                'id'        => $form_id,
                'list_id'   => $form['list_id'],
                'overwrite' => $form['overwrite'],
                'map'       => $form['map']
            ];

            $form_ids[] = $form_id;
        }

        $response = wemail()->api->forms()->integrations( 'gravity-forms' )->post( $settings );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        update_option( 'wemail_form_integration_gravity_forms', $form_ids );

        return $response;
    }

    /**
     * Executes after submit a form
     *
     * @since 1.0.0
     *
     * @param  \WPCF7_ContactForm $wpcf7
     * @param  array $result
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

        if ( ! empty( $data ) ) {
            wemail_set_owner_api_key();
            wemail()->api->forms()->integrations( 'gravity-forms' )->submit()->post( $data );
        }
    }

}
