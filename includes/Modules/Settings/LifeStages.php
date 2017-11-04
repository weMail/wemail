<?php

namespace WeDevs\WeMail\Modules\Settings;

use WeDevs\WeMail\Modules\Settings\AbstractSettings;

class LifeStages extends AbstractSettings {

    /**
     * Menu priority
     *
     * @since 1.0.0
     *
     * @var integer
     */
    public $priority = 20;

    /**
     * Settings menu name
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_menu_name() {
        return __( 'Life Stages', 'wemail' );
    }

    /**
     * Settings data
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_settings() {
        $settings = wemail()->api->settings()->life_stages()->get();

        if ( empty( $settings['i18n'] ) ) {
            $settings['i18n'] = [
                'subscriber'    => __( 'Subscriber', 'wemail' ),
                'lead'          => __( 'Lead', 'wemail' ),
                'opportunity'   => __( 'Opportunity', 'wemail' ),
                'customer'      => __( 'Customer', 'wemail' )
            ];
        }

        return $settings;
    }

    /**
     * companyDetails Route data
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function get_route_data() {
        return [
            'hideSaveButton' => true,
            'settings' => $this->get_settings(),
            'i18n' => [
                'default'         => __( 'default', 'wemail' ),
                'name'            => __( 'Name', 'wemail' ),
                'key'             => __( 'Key', 'wemail' ),
                'makeItDefault'   => __( 'Make it the default life stage', 'wemail' ),
                'remove'          => __( 'Remove', 'wemail' ),
                'cancel'          => __( 'Cancel', 'wemail' ),
                'save'            => __( 'Save', 'wemail' ),
                'nameHint'        => __( 'only lowercased a-z, 0-9 and underscore are allowed', 'wemail' ),
                'ok'              => __( 'OK', 'wemail' ),
                'requiredMsg'     => __( 'Both name and key fields are required', 'wemail' ),
                'errorMsgDefault' => __( 'You cannot remove the default life stage', 'wemail' ),
                'atLeastOneMsg'   => __( 'At least one life stage is required', 'wemail' ),
                'addNewLifeStage' => __( 'Add new life stage', 'wemail' ),
                'setANewNameMsg'  => __( 'Please set a new key for this life stage', 'wemail' ),
            ]
        ];
    }

}
