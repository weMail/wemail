<?php

namespace WeDevs\WeMail\FrontEnd;

use WeDevs\WeMail\Traits\Hooker;

class FrontEnd {

    use Hooker;

    public function __construct() {
        register_widget( '\WeDevs\WeMail\FrontEnd\Widget' );
        $this->add_action( 'template_redirect', 'init' );

        if ( ! is_admin() ) {
            new FormOptIn();
        }
    }

    public function init() {
        wemail_set_owner_api_key( false );

        new Scripts();
        new Shortcodes();

        $this->add_action( 'wp_footer', 'render_form' );
    }

    public function render_form() {
        $forms = wemail()->form->get_forms();

        $current_page_id = get_queried_object_id();
        if ( ! empty( $forms ) ) {
            $forms = $this->get_filtered_forms( $forms, $current_page_id );
            foreach ( $forms as $form ) {
                echo wemail_form( $form );
            }
        }
    }

    protected function get_filtered_forms( $forms, $object_id ) {
        return array_filter(
            $forms,
            function ( $form ) use ( $object_id ) {
                $settings = $form['settings'];
                if ( ! isset( $settings['showPage'], $settings['when'], $settings['who'] ) ) {
                    return false;
                }

                return $this->is_passed_all_checks( $form, $object_id );
            }
        );
    }

    protected function is_passed_all_checks( $form, $object_id ) {
        if ( ! $this->checking_is_modal( $form ) ) {
            return false;
        }

        if ( ! $this->checking_show_page( $form['settings'], $object_id ) ) {
            return false;
        }

        if ( ! $this->checking_schedule( $form['settings'] ) ) {
            return false;
        }

        if ( ! $this->checking_who_see( $form['settings'] ) ) {
            return false;
        }

        return true;
    }

    protected function checking_is_modal( $form ) {
        $settings = $form['settings'];

        if ( $form['type'] !== 'modal' ) {
            return true;
        }

        return $settings['modalTrigger'] === 'auto';
    }

    /**
     * Checking show page condition
     *
     * @param $settings
     * @param $object_id
     *
     * @return bool
     */
    protected function checking_show_page( $settings, $object_id ) {
        switch ( $settings['showPage'] ) {
            case 'all':
                return true;
            case 'home':
                return is_front_page() || is_home();
            case 'specific':
                if ( empty( $settings['pages'] ) ) {
                    return false;
                }
                return $this->in_page_ids($settings['pages'], $object_id);
            case 'except':
                if ( empty( $settings['pages'] ) ) {
                    return false;
                }
                return ! $this->in_page_ids($settings['pages'], $object_id);
        }

        return false;
    }

    /**
     * In page ids
     *
     * @param $page_ids
     * @param $page_id
     *
     * @return bool
     */
    protected function in_page_ids($page_ids, $page_id) {
        return in_array( $page_id, array_map( 'intval', array_column( $page_ids, 'id' ) ), true );
    }

    /**
     * @param $settings
     * @return bool
     */
    protected function checking_schedule( $settings ) {
        switch ( $settings['when'] ) {
            case 'always':
                return true;
            case 'schedule':
                if ( ! isset( $settings['scheduleFrom'], $settings['scheduleTo'] ) ) {
                    return false;
                }
                $now = gmdate( 'Y-m-d' );
                if ( ( $now >= $settings['scheduleFrom'] ) && ( $now <= $settings['scheduleTo'] ) ) {
                    return true;
                }
                break;
        }
        return false;
    }

    protected function checking_who_see( $settings ) {
        switch ( $settings['who'] ) {
            case 'all_users':
                return true;
            case 'logged_users':
                return is_user_logged_in();
            case 'not_logged_users':
                return ! is_user_logged_in();
        }
        return false;
    }
}
