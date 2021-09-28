<?php
namespace WeDevs\WeMail\Core\Ecommerce;

use WeDevs\WeMail\Traits\Singleton;
use WeDevs\WeMail\Core\Ecommerce\Platforms\WooCommerce;
use WeDevs\WeMail\Core\Ecommerce\Platforms\PlatformInterface;

class Ecommerce {
    use Singleton;

    protected $platforms = [
        'woocommerce' => WooCommerce::class
    ];

    /**
     * @param $platform
     *
     * @return PlatformInterface
     */
    public function platform($platform) {
        return $this->platforms[$platform]::instance();
    }

    /**
     * Get platforms
     *
     * @return string[]
     */
    public function getPlatforms() {
        return $this->platforms;
    }
}
