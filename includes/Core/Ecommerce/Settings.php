<?php

namespace WeDevs\WeMail\Core\Ecommerce;

use WeDevs\WeMail\Traits\Singleton;

class Settings {

    use Singleton;

    /**
     * Cache settings data
     *
     * @var array|null $settings
     */
    protected $settings;

    /**
     * @var string $option_name
     */
    protected $option_name = 'wemail_ecommerce';

    /**
     * Get ecommerce settings
     *
     * @return false|mixed|void
     */
    public function get() {
        if ( is_null( $this->settings ) ) {
            $this->settings = get_option( $this->option_name, [] );
        }

        return $this->settings;
    }

    /**
     * Is enabled
     *
     * @return bool
     */
    public function is_enabled() {
        return $this->get_option( 'enabled', false );
    }

    /**
     * Is ecommerce integrated
     *
     * @return bool
     */
    public function is_integrated() {
        return ! empty( $this->get() );
    }

    /**
     * Get customer list ID
     *
     * @return bool|null
     */
    public function list_id() {
        return $this->get_option( 'list_id', '' );
    }

    /**
     * Get single option
     *
     * @param $option_name
     * @param null $default
     *
     * @return bool|null
     */
    public function get_option( $option_name, $default = null ) {
        if ( ! array_key_exists( $option_name, $this->get() ) ) {
            return $default;
        }

        return $this->get()[ $option_name ];
    }

    /**
     * Get ecommerce platform name
     *
     * @return bool|null
     */
    public function platform() {
        return $this->get_option( 'platform' );
    }

    /**
     * Update ecommerce options
     *
     * @param array $options
     *
     * @return bool
     */
    public function update( array $options ) {
        return update_option( $this->option_name, $options );
    }

    /**
     * Delete ecommerce option
     *
     * @return bool
     */
    public function delete() {
        return delete_option( $this->option_name );
    }
}
