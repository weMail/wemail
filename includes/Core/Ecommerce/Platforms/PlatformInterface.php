<?php

namespace WeDevs\WeMail\Core\Ecommerce\Platforms;

interface PlatformInterface {
    public function currency();

    public function currency_symbol();

    public function products();

    public function orders();

    public function customers();

    public function register_hooks();
}
