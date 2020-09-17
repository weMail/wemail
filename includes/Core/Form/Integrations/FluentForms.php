<?php

namespace WeDevs\WeMail\Core\Form\Integrations;

use WeDevs\WeMail\Traits\Singleton;

class FluentForms extends AbstractIntegration {

    use Singleton;

    /**
     * Integration title
     *
     * @var string $title
     */
    public $title = 'Fluent Forms';

    /**
     * Integration slug
     *
     * @var string $slug
     */
    public $slug = 'fluent_forms';

    /**
     * Checking if Fluent Form plugin active or not
     */
    public function boot() {
        $this->is_active = defined( 'FLUENTFORM' );
    }

    /**
     * Get forms
     *
     * @return array|array[]
     * @throws \WpFluent\Exception
     */
    public function forms() {
        $forms = wpFluent()->table( 'fluentform_forms' )->get();

        return array_map(
            function ( $form ) {
                return [
                    'id'     => absint( $form->id ),
                    'title'  => $form->title,
                    'fields' => $this->transform_form_fields( json_decode( $form->form_fields, true ) ),
                ];
            },
            $forms
        );
    }

    /**
     * Transform the form fields
     *
     * @param $fields
     *
     * @return array
     */
    protected function transform_form_fields( $fields ) {
        $data = [];

        foreach ( $fields['fields'] as $field ) {
            if ( ! array_key_exists( 'name', $field['attributes'] ) ) {
                continue;
            }

            if ( $this->has_sub_fields( $field ) ) {
                $data = array_merge( $data, $this->get_sub_fields( $field ) );
                continue;
            }

            $data[] = [
                'id'    => $field['attributes']['name'],
                'label' => $this->get_label( $field['attributes']['name'] ),
            ];
        }

        return $data;
    }

    /**
     * Submit form to the weMail API
     *
     * @param $data
     */
    public function submit( $data ) {
        $settings = get_option( 'wemail_form_integration_fluent_forms', [] );

        if ( ! in_array( intval( $data['form_id'] ), $settings, true ) ) {
            return;
        }

        $submission = [
            'id' => $data['form_id'],
        ];

        $submission['data'] = $this->get_submissions( json_decode( $data['response'], true ) );

        if ( ! empty( $submission['data'] ) ) {
            wemail_set_owner_api_key();
            wemail()->api->forms()->integrations( 'fluent_forms' )->submit()->post( $submission );
        }
    }

    /**
     * Check has sub fields
     *
     * @param $field
     *
     * @return bool
     */
    protected function has_sub_fields( $field ) {
        return array_key_exists( 'fields', $field );
    }

    /**
     * Get sub fields
     *
     * @param $field
     *
     * @return array
     */
    protected function get_sub_fields( $field ) {
        $data = [];

        foreach ( $field['fields'] as $sub_field ) {
            if ( ! array_key_exists( 'name', $sub_field['attributes'] ) ) {
                continue;
            }

            $data[] = [
                'id' => $sub_field['attributes']['name'],
                'label' => $this->get_label( $sub_field['attributes']['name'] ),
            ];
        }

        return $data;
    }

    /**
     * Format for label
     *
     * @param $label
     *
     * @return string
     */
    protected function get_label( $label ) {
        return ucwords( str_replace( [ '-', '_' ], [ ' ', ' ' ], $label ) );
    }

    /**
     * Get submissions fields
     *
     *
     * @param $response
     *
     * @return array
     */
    protected function get_submissions( $response ) {
        foreach ( [ '__fluent_form_embded_post_id', '_fluentform_1_fluentformnonce', '_wp_http_referer' ] as $field ) {
            if ( array_key_exists( $field, $response ) ) {
                unset( $response[ $field ] );
            }
        }

        $data = [];

        foreach ( $response as $field => $value ) {
            if ( is_array( $value ) ) {
                $data = array_merge( $data, $value );
            } else {
                $data[ $field ] = $value;
            }
        }

        return $data;
    }
}
