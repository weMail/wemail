<?php

namespace WeDevs\WeMail\Core\Form\Integrations;

use WeDevs\WeMail\Traits\Singleton;
use WP_Error;

class ContactForm7 {

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
        if ( class_exists( 'WPCF7_ContactForm' ) ) {
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
    public function inactive_message() {
        return new WP_Error(
            'integration_is_not_active',
            __('Contact Form 7 plugin is not active', 'wemail'),
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

        $args = [
            'post_type' => 'wpcf7_contact_form',
            'posts_per_page' => -1,
        ];

        $cf7_query = new \WP_Query( $args );

        if ( ! $cf7_query->have_posts() ) {
            return $forms;

        } else {
            while ( $cf7_query->have_posts() ) {
                $cf7_query->the_post();

                global $post;

                $cf7 = \WPCF7_ContactForm::get_instance( $post->ID );

                $form = [
                    'id' => $post->ID,
                    'name' => $post->post_name,
                    'title' => $post->post_title,
                    'fields' => []
                ];

                foreach ( $cf7->collect_mail_tags() as $tag ) {
                    $form['fields'][] = $tag;
                }

                $forms[] = $form;
            }
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

        $response = wemail()->api->forms()->integrations( 'contact-form-7' )->post( $settings );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        update_option( 'wemail_form_integration_contact_form_7', $form_ids );

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
    public function submit( $wpcf7, $result ) {
        if ( ! empty( $result['invalid_fields'] ) ) {
            return;
        }

        $form_id = $wpcf7->id();

        $settings = get_option( 'wemail_form_integration_contact_form_7', [] );

        if ( ! in_array( $form_id, $settings ) ) {
            return;
        }

        $form_tags   = $wpcf7->scan_form_tags();
        $submission  = \WPCF7_Submission::get_instance();
        $posted_data = $submission->get_posted_data();

        $data = [
            'id' => $form_id
        ];

        foreach ( $form_tags as $form_tag ) {
            $tag = $form_tag->name;

            if ( ! empty( $tag ) && isset( $posted_data[ $tag ] ) ) {
                $data['data'][ $tag ] = $posted_data[ $tag ];
            }
        }

        if ( ! empty( $data ) ) {
            wemail_set_owner_api_key();
            wemail()->api->forms()->integrations( 'contact-form-7' )->submit()->post( $data );
        }
    }

}
