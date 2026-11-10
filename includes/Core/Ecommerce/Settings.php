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
            $this->settings = get_option( $this->option_name, array() );
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
     * Add the fields that only this site can supply
     *
     * The access_token is stamped here so the browser never supplies the site credential.
     *
     * @param \WeDevs\WeMail\Core\Ecommerce\Platforms\PlatformInterface $platform
     * @param array $settings
     *
     * @return array
     */
    public function with_site_fields( $platform, array $settings ) {
        return array_merge(
            $settings, array(
				'currency'              => $platform->currency(),
				'currency_symbol'       => $platform->currency_symbol(),
				'platform'              => $platform->get_name(),
				'subscriptions_enabled' => $platform->is_subscriptions_active(),
				'access_token'          => wemail()->api->get_api_key(),
			)
        );
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
