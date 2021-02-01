<?php

namespace WeDevs\WeMail\Core\Form\Integrations;

use WeDevs\WeMail\Traits\Singleton;

class AffiliateWp extends AbstractIntegration {

    use Singleton;

    /**
     * Integration title
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $title = 'AffiliateWP';

    /**
     * Integration slug
     *
     * @since 1.0.0
     *
     * @var string
     */
    public $slug = 'affiliate_wp';

    /**
     * Execute right after making class instance
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function boot() {
        if ( class_exists( 'Affiliate_WP' ) ) {
            $this->is_active = true;
        }
    }

    /**
     * Get available forms
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function forms() {}
}
