<?php

namespace WeDevs\WeMail\Core\Form\Integrations;

use ElementorPro\Modules\Forms\DB;
use WeDevs\WeMail\Core\Form\Integrations\AbstractIntegration;
use WeDevs\WeMail\Traits\Singleton;
use WP_Query;

class ElementorForms extends AbstractIntegration {

    use Singleton;

    /**
     * Integration title
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $title = 'Elementor Form';

    /**
     * Integration slug
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $slug = 'elementor_forms';

    /**
     * Execute right after making class instance
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function boot() {
        if ( class_exists( 'ElementorPro\Plugin' ) ) {
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
        $forms = array();

        $args = array(
            'post_type' => 'any',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_elementor_data',
                    'compare' => 'EXISTS',
                ),
            ),
        );

        $query = new WP_Query( $args );

        if ( $query->have_posts() ) {
            // Loop through the posts
            while ( $query->have_posts() ) {
                $query->the_post();

                foreach ( json_decode( get_post_meta( get_the_ID(), '_elementor_data', true ) ) as $item ) {
                    foreach ( $item->elements as $element ) {
                        foreach ( $element->elements as $final_element ) {
                            if ( isset( $final_element->{'widgetType'} ) && $final_element->{'widgetType'} === 'form' ) {
                                $forms[] = array(
                                    'id' => $final_element->id,
                                    'title' => $this->getTitle( $final_element->id, $final_element->settings->form_name ),
                                    'fields' => $this->get_fields( $final_element->settings->form_fields ),
                                );
                            }
                        }
                    }
                }
            }
        }
        return array_filter(
            $forms, function ( $form ) {
				return ! empty( $form['fields'] );
			}
        );
    }

    /**
     * @param $name
     * @param $id
     * @return mixed
     */
    public function getTitle( $id, $name ) {
        return $name . ' | Form ID: ' . $id;
    }

    /**
     * @param $form_fields
     * @return mixed
     */
    public function get_fields( $form_fields ) {
        $fields = array();

        foreach ( $form_fields as $field ) {
            $fields[] = array(
                'id' => $field->custom_id,
                'label' => isset( $field->field_label ) ? $field->field_label : $field->custom_id . ' (Set label)',
            );
        }

        return $fields;
    }

    /**
     * Capture submission data
     *
     * @param $data
     * @param $entry
     */
    public function submit( $record, $handler ) {
        $raw_fields = $record->get( 'fields' );
        $fields = array();
        $form_id = $record->get( 'form_settings' )['id'];
        foreach ( $raw_fields as $id => $field ) {
            $fields[ $id ] = $field['value'];
        }

        $settings = get_option( 'wemail_form_integration_elementor_forms', array() );

		if ( ! in_array( $form_id, $settings, true ) ) {
			return;
		}

        $entities = array(
            'id' => $form_id,
            'data' => $fields,
        );

        if ( ! empty( $entities['data'] ) ) {
            wemail_set_owner_api_key();
            wemail()->api->forms()->integrations( 'elementor_forms' )->submit()->post( $entities );
        }
    }
}
