<?php

namespace WeDevs\WeMail\Modules\Settings;

use WeDevs\WeMail\Traits\Hooker;

class Routes {

    use Hooker;

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->add_filter( 'wemail_get_route_data_settings', 'get_route_data' );
    }

    /**
     * Settings route data
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_route_data() {
        return [
            'settings' => array_map( [ $this, 'map_settings' ], wemail()->settings->settings )
        ];
    }

    /**
     * Map settings callback method
     *
     * @since 1.0.0
     *
     * @param object $setting
     *
     * @return array
     */
    public function map_settings( $setting ) {
        return [
            'name'  => $setting->name,
            'path'  => $setting->path,
            'title' => $setting->title
        ];
    }
}
