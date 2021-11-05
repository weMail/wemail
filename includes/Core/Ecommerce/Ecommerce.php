<?php
namespace WeDevs\WeMail\Core\Ecommerce;

use WeDevs\WeMail\Core\Ecommerce\Platforms\AbstractPlatform;
use WeDevs\WeMail\Core\Ecommerce\Platforms\EDD;
use WeDevs\WeMail\Traits\Singleton;
use WeDevs\WeMail\Core\Ecommerce\Platforms\WooCommerce;
use WeDevs\WeMail\Core\Ecommerce\Platforms\PlatformInterface;

class Ecommerce {
    use Singleton;

    protected $platforms = [
        'woocommerce' => WooCommerce::class,
        'edd' => EDD::class,
    ];

    /**
     * @param $platform
     *
     * @return AbstractPlatform
     */
    public function platform( $platform ) {
        return $this->platforms[ $platform ]::instance();
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
