<?php

namespace WeDevs\WeMail\Modules\Settings;

use WeDevs\WeMail\Framework\Traits\Ajax;
use Stringy\StaticStringy;

abstract class AbstractSettings {

    use Ajax;

    /**
     * Route endpoint
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $path;

    /**
     * Vue route name
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $route_name;

    /**
     * Vue component name
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $component;

    /**
     * Settings menu name
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $menu;

    /**
     * Parent Settings class
     *
     * @var WeDevs\WeMail\Modules\Settings\Settings
     */
    public $parent;

    /**
     * Class constructor
     *
     * This will provide path, route_name, component and menu properties.
     * Example properties are shown for CompanyDetails class as comment
     * at right.
     *
     * @since 1.0.0
     *
     * @param \WeDevs\WeMail\Modules\Settings\Settings $parent
     */
    public function __construct( Settings $parent ) {
        $class_name       = explode( "\\", get_class( $this ) );
        $class_name       = array_pop( $class_name ); // CompanyDetails
        $this->path       = StaticStringy::dasherize( $class_name ); // company-details
        $this->route_name = StaticStringy::camelize( $class_name ); // companyDetails
        $this->component  = $class_name; // CompanyDetails
        $this->menu       = $this->get_menu_name(); // __( 'Company Details', 'wemail' )

        $this->parent = $parent;
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
