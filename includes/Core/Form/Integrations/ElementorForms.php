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
        $forms = [];

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

        $query = new WP_Query($args);

        if ($query->have_posts()) {
            // Loop through the posts
            while ($query->have_posts()) {
                $query->the_post();
                global $post;

                error_log(print_r(get_post_meta(get_the_ID(), '_elementor_data', true), true));
//
//                foreach (json_decode(get_post_meta(get_the_ID(), '_elementor_data', true)) as $item) {
//                    error_log(print_r($item->elements, true));
//                }

                $forms[] = [
                    'id' => get_the_ID(),
                    'title' => $this->getTitle(get_post_meta(get_the_ID(), '__elementor_forms_snapshot', true)),
                ];
            }
        }

        return array_filter(
            $forms, function( $form ) {
				return ! empty( $form['fields'] );
			}
        );
    }

    /**
     * @param $post
     * @return mixed
     */
    public function getTitle ($post) {

    }

    /**
     * @param $post
     * @return mixed
     */
//    public function get_fields( $post ) {
//        $fields = [];
//
//        $content = json_decode( $post->post_content );
//
//        if ( ! isset( $content->form_fields ) ) {
//            return $fields;
//        }
//
//        $get_columns = get_object_vars( $content->form_fields );
//        foreach ( $get_columns as $get_column ) {
//            $fields[] = [
//                'id' => $get_column->id,
//                'label' => $get_column->label,
//            ];
//        }
//
//        return $fields;
//    }

    /**
     * Capture submission data
     *
     * @param $data
     * @param $entry
     */
    public function submit( $data, $entry ) {

        $form_data = $data->get_formatted_data();

        error_log( print_r($data, true));

//        $this->data = $entry['form_fields'];
//
//        $settings = get_option( 'wemail_form_integration_everest_forms', [] );
//
//		if ( ! in_array( (int) $entry['id'], $settings, true ) ) {
//			return;
//		}
//
//        $entities = [
//            'id' => $entry['id'],
//            'data' => $this->data,
//        ];
//
//        if ( ! empty( $entities['data'] ) ) {
//            wemail_set_owner_api_key();
//            wemail()->api->forms()->integrations( 'everest_forms' )->submit()->post( $entities );
//        }
    }

}
