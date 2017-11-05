<?php

namespace WeDevs\WeMail\Modules\Settings;

use Stringy\StaticStringy;

abstract class AbstractSettings {

    /**
     * Settings name
     *
     * snake_cased string
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $name;

    /**
     * The URL slug for related settings
     *
     * kebab-cased string
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $path;

    /**
     * Settings title
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $title;

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $class_name = explode( "\\", get_class( $this ) );
        $class_name = array_pop( $class_name ); // LifeStage
        $this->name = StaticStringy::underscored( $class_name ); // life-stage
        $this->path = StaticStringy::dasherize( $class_name ); // life-stage
        $this->title = $this->get_title(); // __( 'Life Stage', 'wemail' )

        add_filter( "wemail_get_route_data_settings{$class_name}", [ $this, 'get_route_data' ] );
    }

    /**
     * i18n title for the settings
     *
     * @since 1.0.0
     *
     * @return string
     */
    abstract public function get_title();

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
