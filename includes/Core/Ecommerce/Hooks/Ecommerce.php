<?php

namespace WeDevs\WeMail\Core\Ecommerce\Hooks;

use WeDevs\WeMail\Core\Ecommerce\Settings;
use WeDevs\WeMail\Traits\Singleton;

class Ecommerce {

    use Singleton;

    /**
     * Registering ecommerce platforms hooks
     */
    public function boot() {
        foreach ( wemail()->ecommerce->getPlatforms() as $platform ) {
            if ( $platform::instance()->is_active() ) {
                $platform::instance()->register_hooks();
            }
        }

        add_action( 'wemail_site_authenticated', array( $this, 'restore_settings' ) );
    }

    /**
     * Restore the local ecommerce option when a site connects
     *
     * Deleting the plugin removes the option but the cloud keeps the
     * integration, so the settings screen says it is on while the store
     * sends nothing to weMail.
     *
     * @return void
     */
    public function restore_settings() {
        if ( Settings::instance()->is_integrated() ) {
            return;
        }

        $response = wemail()->api->ecommerce()->settings()->get();

        if ( is_wp_error( $response ) || empty( $response['settings']['platform'] ) ) {
            return;
        }

        $settings = $response['settings'];

        if ( ! array_key_exists( $settings['platform'], wemail()->ecommerce->getPlatforms() ) ) {
            return;
        }

        $platform = wemail()->ecommerce->platform( $settings['platform'] );

        if ( ! $platform->is_active() ) {
            return;
        }

        Settings::instance()->update( Settings::instance()->with_site_fields( $platform, $settings ) );
    }
}
