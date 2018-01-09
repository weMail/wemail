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
     * Settings title
     *
     * @since 1.0.0
     *
     * @return string
     */
    public function get_title() {
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
        $default = [
            'names' => [],
            'i18n' => [],
            'default' => '',
        ];

        $settings = wemail()->api->settings()->life_stages()->get();

        return !empty($settings['settings']) ? $settings['settings'] : $default;
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
