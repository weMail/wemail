<?php

namespace WeDevs\WeMail\Core\Form\Integrations;

use WeDevs\WeMail\Traits\Singleton;

class PopupMaker extends AbstractIntegration {

    use Singleton;

    /**
     * Integration title
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $title = 'Popup Maker';

    /**
     * Integration slug
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $slug = 'popup_maker';

    /**
     * Execute right after making class instance
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function boot() {
        if ( class_exists( 'Popup_Maker' ) ) {
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
        $popups = pum_get_all_popups();

        foreach ( $popups as $popup ) {
            $forms[] = [
                'id'     => $popup->ID,
                'title'  => $popup->post_title ? $popup->post_title : 'no title',
                'fields' => $this->get_fields( $popup->post_content, $popup->ID ),
            ];
        }

        return $forms;
    }

    private function get_fields( $content, $id ) {


//        $content = apply_filters( 'pum_popup_content', $content, $id );
        return $content;

    }

    /**
     * Executes after submit a form
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function submit( ) {

    }

}
