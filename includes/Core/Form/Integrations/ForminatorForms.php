<?php

namespace WeDevs\WeMail\Core\Form\Integrations;

use WP_Query;
use WeDevs\WeMail\Traits\Singleton;

class ForminatorForms extends AbstractIntegration {

    use Singleton;

    /**
     * @inheritdoc
     */
    public $title = 'Forminator Forms';

    /**
     * @inheritdoc
     */
    public $slug = 'forminator_forms';

    public function boot() {
        $this->is_active = class_exists( 'Forminator' );
    }

    /**
     * @inheritdoc
     */
    public function forms() {
        $forms = array();
        $query = new WP_Query(
            array(
                'post_type'     => 'forminator_forms',
                'post_count'    => -1,
            )
        );

        if ( ! $query->have_posts() ) {
            return $forms;
        }

        while ( $query->have_posts() ) {
            $query->the_post();
            $forms[] = array(
                'id'        => get_the_ID(),
                'title'     => get_the_title(),
                'fields'    => $this->get_form_fields( get_the_ID() ),
            );
        }

        return $forms;
    }

    /**
     * Get form fields
     *
     * @param $form_id
     * @return array
     */
    protected function get_form_fields( $form_id ) {
        $form_meta = get_post_meta( $form_id, 'forminator_form_meta', true );
        if ( ! $form_meta ) {
            return array();
        }

        $fields = array();

        foreach ( $form_meta['fields'] as $field ) {
            if ( ! isset( $field['field_label'] ) ) {
                continue;
            }

            $fields[] = array(
                'id'    => $field['id'],
                'label' => $field['field_label'],
            );
        }
        return $fields;
    }

    /**
     * Handle form submission
     *
     * @param $entry
     * @param $form_id
     * @param $form_data
     */
    public function submit( $entry, $form_id, $form_data ) {
        $forms = get_option( 'wemail_form_integration_forminator_forms', array() );
        $form_id = absint( $form_id );

        if ( ! in_array( $form_id, $forms, true ) || empty( $form_data ) ) {
            return;
        }

        $data = array(
            'id'    => $form_id,
            'data'  => array_column( $form_data, 'value', 'name' ),
        );

        wemail_set_owner_api_key();
        wemail()->api->forms()->integrations( 'forminator-forms' )->submit()->post( $data );
    }
}
