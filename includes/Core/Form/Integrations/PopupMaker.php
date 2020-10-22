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
            $fields = $this->get_fields( $popup->post_content );
            if ( ! $fields ) {
                continue;
            }

            $forms[] = [
                'id'     => $popup->ID,
                'title'  => $popup->post_title ? $popup->post_title : 'no title',
                'fields' => $fields,
            ];
        }

        return $forms;
    }

    private function get_fields( $content ) {
        $data = explode( '[', rtrim( $content, ']' ) );
        if ( ! $data || ! isset( $data[1] ) ) {
            return false;
        }
        $data = $data[1];
        $data = str_replace( '"', "'", $data );
        $data = stripcslashes( $data );
        $data = explode( ' ', $data );

        if ( ! $data || $data[0] !== 'pum_sub_form' ) {
            return false;
        }

        $fields = [];
        foreach ( $data as $item ) {
            $item = explode( '=', $item );
            if ( ! isset( $item[0] ) ) {
                continue;
            }

            $field = $this->field_data( $item[0] );
            if ( $field ) {
                $fields[] = $field;
            }
        }

        return $fields;
    }

    private function field_data( $data ) {
        switch ( $data ) {
            case 'label_fname':
                return [
                    'id'    => 'first-name',
                    'label' => 'First Name',
                ];
            case 'label_lname':
                return [
                    'id'    => 'last-name',
                    'label' => 'Last Name',
                ];
            case 'label_name':
                return [
                    'id'    => 'name',
                    'label' => 'Name',
                ];
            case 'label_email':
                return [
                    'id'    => 'email',
                    'label' => 'Email',
                ];
            default:
                return null;
        }
    }

    /**
     * Executes after submit a form
     *
     * @param $values
     * @return void
     * @since 1.0.0
     */
    public function submit( $values ) {
        if ( ! $values ) {
            return;
        }

        $user_data = [
            'email'      => isset( $values['email'] ) ? $values['email'] : '',
            'name'       => isset( $values['name'] ) ? $values['name'] : '',
            'first-name' => isset( $values['fname'] ) ? $values['fname'] : '',
            'last-name'  => isset( $values['lname'] ) ? $values['lname'] : '',
        ];

        $form_data = [
            'id'    => $values['popup_id'],
            'data'  => $user_data,
        ];

        if ( ! empty( $form_data['data'] ) ) {
            wemail_set_owner_api_key();
            wemail()->api->forms()->integrations( 'popup-maker' )->submit()->post( $form_data );
        }
    }

}
