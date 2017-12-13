<?php

namespace WeDevs\WeMail\Modules\Settings;

use WeDevs\WeMail\Modules\Settings\AbstractSettings;

class EmailOutbound extends AbstractSettings {

    /**
     * Menu priority
     *
     * @since 1.0.0
     *
     * @var integer
     */
    public $priority = 200;

    /**
     * Settings title
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_title() {
        return __( 'Email Outbound', 'wemail' );
    }

    /**
     * Settings data
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_settings() {
        $settings = wemail()->api->settings()->email_outbound()->get();

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
            'settings' => $this->get_settings()
        ];
    }

}
