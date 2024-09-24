<?php

namespace WeDevs\WeMail\Core\Form\Integrations;

use WeDevs\WeMail\Core\Form\Integrations\AbstractIntegration;
use WeDevs\WeMail\Traits\Singleton;
use WP_Query;

class EverestForms extends AbstractIntegration {

    use Singleton;

    /**
     * Integration title
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $title = 'Everest Forms';

    /**
     * Integration slug
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $slug = 'everest_forms';

    /**
     * Execute right after making class instance
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function boot() {
        if ( class_exists( 'EverestForms' ) ) {
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

        $query = new WP_Query(
            array(
                'post_type' => 'everest_form',
                'post_per_page' => -1,
            )
        );
        while ( $query->have_posts() ) {
            $query->the_post();
            global $post;

            $forms[] = array(
                'id' => absint( $post->ID ),
                'title' => $post->post_title,
                'fields' => $this->get_fields( $post ),
            );
        }

        return array_filter(
            $forms, function ( $form ) {
				return ! empty( $form['fields'] );
			}
        );
    }

    /**
     * @param $post
     * @return mixed
     */
    public function get_fields( $post ) {
        $fields = array();

        $content = json_decode( $post->post_content );

        if ( ! isset( $content->form_fields ) ) {
            return $fields;
        }

        $get_columns = get_object_vars( $content->form_fields );
        foreach ( $get_columns as $get_column ) {
            $fields[] = array(
                'id' => $get_column->id,
                'label' => $get_column->label,
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
    public function submit( $data, $entry ) {
        $this->data = $entry['form_fields'];

        $settings = get_option( 'wemail_form_integration_everest_forms', array() );

		if ( ! in_array( (int) $entry['id'], $settings, true ) ) {
			return;
		}

        $entities = array(
            'id' => $entry['id'],
            'data' => $this->data,
        );

        if ( ! empty( $entities['data'] ) ) {
            wemail_set_owner_api_key();
            wemail()->api->forms()->integrations( 'everest_forms' )->submit()->post( $entities );
        }
    }
}
