<?php

namespace WeDevs\WeMail\Core\Form;

use WeDevs\WeMail\Traits\Singleton;

class Form {

    use Singleton;

    /**
     * Get paginated list of forms
     *
     * @since 1.0.0
     *
     * @param array $query
     *
     * @return array
     */
    public function all( $query ) {
        return wemail()->api->forms()->query( $query )->get();
    }

    /**
     * Get a single form data
     *
     * @since 1.0.0
     *
     * @param string $id
     *
     * @return array
     */
    public function get( $id ) {
        $form =  wemail()->api->forms($id)->get();

        if ( ! empty( $form['data'] ) ) {
            return $form['data'];
        }

        return null;
    }

    /**
     * Get all form items
     *
     * id-name paired items
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function items() {
        $forms = wemail()->api->forms()->items()->get();

        if ( is_wp_error( $forms ) ) {
            return $forms;
        } else if ( ! empty( $forms['data'] ) ) {
            return $forms['data'];
        }

        return null;
    }

    /**
     * Post a form submission
     *
     * @since 1.0.0
     *
     * @param string $id
     * @param array $data
     *
     * @return null|array|WP_Error
     */
    public function submit( $id, $data ) {
        $form = wemail()->api->forms( $id )->submit()->post( $data );

        if ( is_wp_error( $form ) ) {
            return $form;
        } else if ( ! empty( $form['data'] ) ) {
            return $form['data'];
        }

        return null;
    }

    /**
     * Name of supported form integrations
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function integrations() {
        return [
            'contact_form_7' => __( 'Contact Form 7', 'wemail' ),
            'gravity_forms' => __( 'Gravity Forms', 'wemail' )
        ];
    }
}
