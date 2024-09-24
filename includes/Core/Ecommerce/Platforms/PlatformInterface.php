<?php

namespace WeDevs\WeMail\Core\Ecommerce\Platforms;

interface PlatformInterface {
    public function is_active();

    public function currency();

    public function currency_symbol();

    public function products( array $args = array() );

    public function orders( array $args = array() );

    public function categories( array $args = array() );

    public function register_hooks();

    public function get_name();
}
