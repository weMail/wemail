<?php

namespace WeDevs\WeMail\FrontEnd;

use WeDevs\WeMail\Traits\Hooker;

class FrontEnd {

    use Hooker;

    public function __construct() {
        register_widget( '\WeDevs\WeMail\FrontEnd\Widget' );
        $this->add_action( 'template_redirect', 'init' );
        new FormOptIn();
    }

    public function init() {
        wemail_set_owner_api_key();

        new Scripts();
        new Shortcodes();

        $this->add_filter('the_content', 'render_form');
    }

    public function render_form( $content ) {
        $forms = wemail()->form->all([
            'type' => 'floating-bar,floating-box,modal',
            'fields' => 'id,settings',
            'status' => 1
        ])['data'];

        $current_page_id = get_queried_object_id();

        if ( ! empty( $forms ) ) {

            $forms = $this->get_filtered_forms( $forms, $current_page_id );

            foreach ( $forms as $form ) {
                $content .= do_shortcode('[wemail_form id="' . $form['id'] . '"]');
            }
        }

        return $content;
    }

    protected function get_filtered_forms( $forms, $object_id ) {

        return array_filter( $forms, function ($form) use ( $object_id ){
            $settings = $form['settings'];

            if ( ! isset( $settings['showPage'], $settings['when'], $settings['who'] ) ) {
                return false;
            }

            if ( $this->checking_show_page( $settings, $object_id )
                && $this->checking_schedule( $settings )
                && $this->checking_who_see( $settings ) ) {
                return true;
            }

            return false;
        });
    }

    protected function checking_show_page( $settings, $object_id ) {
        switch ( $settings['showPage'] ) {
            case 'all':
                return true;
                break;
            case 'home':
                return is_front_page() || is_home();
                break;
            default:
                if ( empty( $settings['pages'] ) ) {
                    return false;
                }
                return in_array( $object_id, array_column( $settings['pages'], 'id') );
        }
    }

    /**
     * @param $settings
     * @return bool
     */
    protected function checking_schedule($settings) {
        switch ( $settings['when'] ) {
            case 'always':
                return true;
                break;
            case 'schedule':
                if (!isset( $settings['scheduleFrom'], $settings['scheduleTo'] )) {
                    return false;
                }
                $now   = date('Y-m-d');

                if ( ($now >= $settings['scheduleFrom']) && ($now <= $settings['scheduleTo']) ) {
                    return true;
                }

                break;
        }

        return false;
    }

    protected function checking_who_see( $settings )
    {
        switch ( $settings['who'] ) {
            case 'all_users':
                return true;
                break;

            case 'logged_users':
                return is_user_logged_in();
                break;

            case 'not_logged_users':
                return ! is_user_logged_in();
                break;
        }

        return false;
    }

}

