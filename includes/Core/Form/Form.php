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
}
