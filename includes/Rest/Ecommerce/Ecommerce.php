<?php

namespace WeDevs\WeMail\Rest\Ecommerce;

use WeDevs\WeMail\RestController;
use WeDevs\WeMail\Core\Ecommerce\Settings;
use WeDevs\WeMail\Core\Ecommerce\Platforms\PlatformInterface;

class Ecommerce extends RestController
{
    /**
     * @inheritdoc
     */
    protected $rest_base = 'ecommerce';

    /**
     * @inheritdoc
     */
    public function register_routes() {
        $this->post('/(?P<source>woocommerce|edd)/settings', 'settings');
        $this->get('/(?P<source>woocommerce|edd)/products', 'products');
    }

    /**
     * Settings endpoint
     *
     * @param \WP_REST_Request $request
     */
    public function settings($request) {
        $body = $request->get_json_params();
        $platform = $request->get_param('source');

        /** @var PlatformInterface $platform */
        $platform = wemail()->ecommerce->platform($platform);

        $body += [
            'currency' =>$platform->currency(),
            'currency_symbol' =>$platform->currency_symbol(),
        ];
        // Update settings to wp options
        Settings::instance()->update($body);

        return wemail()->api->ecommerce()->settings()->post($body);
    }

    /**
     * @param \WP_REST_Request $request
     */
    public function products($request) {
        $platform = $request->get_param( 'source' );

        return wemail()->ecommerce->platform($platform)->products();
    }
}
