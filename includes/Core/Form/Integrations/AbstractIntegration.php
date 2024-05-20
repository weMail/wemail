<?php

namespace WeDevs\WeMail\Core\Form\Integrations;

use WeDevs\WeMail\Traits\Stringy;
use WP_Error;

abstract class AbstractIntegration {
    use Stringy;

    /**
     * Hold the value if plugin active or not
     *
     * @since 1.0.0
     *
     * @var bool
     */
    public $is_active = false;

    /**
     * Integration title
     *
     * For example: Contact Form 7, Gravity Forms etc
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $title;

    /**
     * Integration slug
     *
     * For example: contact_form_7, gravity_forms etc
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $slug;

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
            /* translators: %s: search term */
            sprintf( __( '%s plugin is not active', 'wemail' ), $this->title ),
            [ 'status' => 422 ]
        );
    }

    /**
     * Get available forms
     *
     * @since 1.0.0
     *
     * @return array
     */
    abstract public function forms();

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
        return absint( $form_id );
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

            $form_id = $this->slug === 'elementor_forms' ? $form['id'] : $this->cast_form_id( $form['id'] );

            $settings[] = [
                'id'        => $form_id,
                'list_id'   => $form['list_id'],
                'overwrite' => $form['overwrite'],
                'map'       => $form['map'],
            ];

            $form_ids[] = $form_id;
        }

        $response = wemail()->api->forms()->integrations( $this->dasherize( $this->slug ) )->post( $settings );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        update_option( "wemail_form_integration_{$this->slug}", $form_ids );

        return $response;
    }

}
