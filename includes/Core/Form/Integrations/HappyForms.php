<?php

namespace WeDevs\WeMail\Core\Form\Integrations;

use WeDevs\WeMail\Traits\Singleton;
use WP_Query;

class HappyForms extends AbstractIntegration {

    use Singleton;

    /**
     * Integration title
     *
     * @var string $title
     */
    public $title = 'Happy Forms';

    /**
     * Integration slug
     *
     * @var string $slug
     */
    public $slug = 'happy_forms';

    /**
     * Submission data
     *
     * @var array $data
     */
    public $data = array();

    /**
     * Checking plugin is active or not
     */
    public function boot() {
        $this->is_active = defined( 'HAPPYFORMS_VERSION' );
    }

    /**
     * Get all forms
     *
     * @return array
     */
    public function forms() {
        $forms = array();

        $query = new WP_Query(
            array(
                'post_type' => 'happyform',
                'posts_per_page' => -1,
            )
        );

        while ( $query->have_posts() ) {
            $query->the_post();
            global $post;

            $forms[] = array(
                'id' => absint( $post->ID ),
                'title' => $post->post_title,
                'fields' => $this->get_fields( $post->ID ),
            );
        }

        return $forms;
    }

    /**
     * Get form fields
     *
     * @param $post_id
     *
     * @return array
     */
    protected function get_fields( $post_id ) {
        $fields = array();

        $data = get_post_meta( $post_id );

        $columns = $this->get_columns( $data['_happyforms_layout'] );

        foreach ( $columns as $column ) {
            if ( ! array_key_exists( '_happyforms_' . $column, $data ) ) {
                continue;
            }

            foreach ( $data[ '_happyforms_' . $column ] as $meta_field ) {
                $fields[] = $this->get_field( unserialize( $meta_field ) );
            }
        }

        return $fields;
    }

    /**
     * Get form field
     *
     * @param $field
     *
     * @return array
     */
    protected function get_field( $field ) {
        return array(
            'id'    => $field['id'],
            'label' => $field['label'],
        );
    }

    /**
     * Get all columns
     *
     * @param $layouts
     *
     * @return array
     */
    protected function get_columns( $layouts ) {
        $columns = array();

        foreach ( $layouts as $layout ) {
            $columns = array_merge( $columns, unserialize( $layout ) );
        }

        return array_unique( $columns );
    }

    /**
     * Capture submission data
     *
     * @param $data
     */
    public function submit( $data ) {
        $this->data = $data;

        add_action( 'happyforms_form_submit_after', array( $this, 'subscribe' ) );
    }

    /**
     * Subscribe subscriber
     *
     * @param $form
     */
    public function subscribe( $form ) {
        $form_ids = get_option( 'wemail_form_integration_happy_forms', array() );

        if ( ! in_array( $form['ID'], $form_ids, true ) ) {
            return;
        }

        $data = array(
            'id' => $form['ID'],
            'data' => $this->data,
        );

        if ( ! empty( $data['data'] ) ) {
            wemail_set_owner_api_key();
            wemail()->api->forms()->integrations( 'happy_forms' )->submit()->post( $data );
        }
    }
}
