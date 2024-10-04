<?php

namespace WeDevs\WeMail\Core\Form\Integrations;

use WP_Query;
use WeDevs\WeMail\Core\Form\Integrations\AbstractIntegration;
use WeDevs\WeMail\Traits\Singleton;

class ContactForm7 extends AbstractIntegration {

    use Singleton;

    /**
     * Integration title
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $title = 'Contact Form 7';

    /**
     * Integration slug
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $slug = 'contact_form_7';

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
     * Get available forms
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function forms() {
        $forms = array();

        $args = array(
            'post_type' => 'wpcf7_contact_form',
            'posts_per_page' => -1,
        );

        $cf7_query = new WP_Query( $args );

        if ( ! $cf7_query->have_posts() ) {
            return $forms;
        } else {
            while ( $cf7_query->have_posts() ) {
                $cf7_query->the_post();

                global $post;

                $cf7 = \WPCF7_ContactForm::get_instance( $post->ID );

                $form = array(
                    'id'     => $post->ID,
                    'title'  => $post->post_title,
                    'fields' => array(),
                );

                foreach ( $cf7->collect_mail_tags() as $tag ) {
                    $form['fields'][] = array(
                        'id'    => $tag,
                        'label' => "[{$tag}]",
                    );
                }

                $forms[] = $form;
            }
        }

        return $forms;
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

        $settings = get_option( 'wemail_form_integration_contact_form_7', array() );

        if ( ! in_array( $form_id, $settings, true ) ) {
            return;
        }

        $form_tags   = $wpcf7->scan_form_tags();
        $submission  = \WPCF7_Submission::get_instance();
        $posted_data = $submission->get_posted_data();

        $data = array(
            'id' => $form_id,
        );

        foreach ( $form_tags as $form_tag ) {
            $tag = $form_tag->name;

            if ( ! empty( $tag ) && isset( $posted_data[ $tag ] ) ) {
                $data['data'][ $tag ] = $posted_data[ $tag ];
            }
        }

        if ( ! empty( $data['data'] ) ) {
            wemail_set_owner_api_key();
            wemail()->api->forms()->integrations( 'contact-form-7' )->submit()->post( $data );
        }
    }
}
