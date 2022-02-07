<?php

namespace WeDevs\WeMail\Core\Ecommerce\Platforms;

interface PlatformInterface {
    public function is_active();

    public function currency();

    public function currency_symbol();

    public function products( array $args = []);

    public function orders( array $args = [] );

    public function categories( array $args = [] );

    public function register_hooks();

    public function get_name();
}
