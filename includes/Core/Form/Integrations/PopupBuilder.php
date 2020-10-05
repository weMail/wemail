<?php

namespace WeDevs\WeMail\Core\Form\Integrations;

use sgpb\Ajax;
use sgpb\SubscriptionPopup;
use WeDevs\WeMail\Traits\Singleton;

class PopupBuilder extends AbstractIntegration {

    use Singleton;

    /**
     * Integration title
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $title = 'Popup Builder';

    /**
     * Integration slug
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $slug = 'popup_builder';

    /**
     * Execute right after making class instance
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function boot() {
        $this->is_active = defined( 'SG_POPUP_TEXT_DOMAIN' );
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

        $subscription_popup = new SubscriptionPopup();
        $subscription_popups = $subscription_popup->getAllSubscriptions();

        if ( ! $subscription_popups || count( $subscription_popups ) === 0 ) {
            return $forms;
        }

        foreach ( $subscription_popups as $popup ) {
            $forms[] = [
                'id'     => $popup->getId(),
                'title'  => $popup->getTitle() ? $popup->getTitle() : 'no title',
                'fields' => $this->get_fields( $popup ),
            ];
        }

        return $forms;
    }

    /**
     * Get fields
     *
     * @param $popup
     *
     * @return array|void
     */
    protected function get_fields( $popup ) {
        $subs_fields = $popup->getOptionValue( 'sgpb-subs-fields' );
        if ( empty( $subs_fields ) ) {
            return [];
        }

        $fields = [];
        foreach ( $subs_fields as $key => $field ) {
            if ( ! in_array( $field['attrs']['type'], [ 'text', 'email' ], true ) ) {
                continue;
            }

            if ( ! isset( $field['attrs']['placeholder'] ) || $field['attrs']['placeholder'] === '' ) {
                continue;
            }

            $fields[] = [
                'id'    => $key,
                'label' => $field['attrs']['placeholder'],
            ];
        }

        return $fields;
    }

    /**
     * Executes after submit a form
     *
     * @param $data
     * @return void
     * @since 1.0.0
     */
    public function submit( $data ) {
        $forms = get_option( 'wemail_form_integration_popup_builder', [] );

        check_ajax_referer( SG_AJAX_NONCE, 'nonce' );
        $sgpb_ajax = new Ajax();
        $sgpb_ajax->setPostData( $_POST );
        $popup_post_id = (int) $sgpb_ajax->getValueFromPost( 'popupPostId' );

        if ( ! in_array( $popup_post_id, $forms, true ) ) {
            return;
        }

        $submission_data = $sgpb_ajax->getValueFromPost( 'formData' );
        parse_str( $submission_data, $form_data );

        $user_data = [
            'email'      => sanitize_email( $form_data['sgpb-subs-email'] ),
            'first-name' => sanitize_text_field( $form_data['sgpb-subs-first-name'] ),
            'last-name'  => sanitize_text_field( $form_data['sgpb-subs-last-name'] ),
        ];

        $form_data = [
            'id'    => $popup_post_id,
            'data'  => $user_data,
        ];

        if ( ! empty( $form_data['data'] ) ) {
            wemail_set_owner_api_key();
            wemail()->api->forms()->integrations( 'popup-builder' )->submit()->post( $form_data );
        }
    }
}
