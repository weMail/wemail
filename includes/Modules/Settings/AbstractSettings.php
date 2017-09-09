<?php

namespace WeDevs\WeMail\Modules\Settings;

use WeDevs\WeMail\Framework\Traits\Ajax;
use Stringy\StaticStringy;

abstract class AbstractSettings {

    use Ajax;

    /**
     * Vue route name
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $route_name;

    /**
     * Settings menu name
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $menu;

    /**
     * Class constructor
     *
     * This will provide path, route_name, component and menu properties.
     * at right.
     *
     * @since 1.0.0
     *
     * @param \WeDevs\WeMail\Modules\Settings\Settings $parent
     */
    public function __construct() {
        $class_name       = explode( "\\", get_class( $this ) );
        $class_name       = array_pop( $class_name ); // SocialNetworks
        $this->route_name = 'settings' . StaticStringy::upperCamelize( $class_name ); // settingsSocialNetworks
        $this->menu       = $this->get_menu_name(); // __( 'Social Networks', 'wemail' )
    }

    /**
     * Menu name in Settings page
     *
     * @since 1.0.0
     *
     * @return string
     */
    abstract public function get_menu_name();

    /**
     * Get settings data
     *
     * @since 1.0.0
     *
     * @return array
     */
    abstract public function get_settings();

    /**
     * Get route data
     *
     * @since 1.0.0
     *
     * @return void
     */
    abstract public function get_route_data();

}
