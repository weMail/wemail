<?php

namespace WeDevs\WeMail\Core\Help;

use WeDevs\WeMail\Core\Help\Services\PluginsInfo;
use WeDevs\WeMail\Core\Help\Services\SystemService;
use WeDevs\WeMail\Core\Help\Services\WordpressInfo;

class SystemInfo {

    protected $system;

    protected $wp;

    protected $plugins;

    public function __construct() {
        $this->system = new SystemService();
        $this->wp = new WordpressInfo();
        $this->plugins = new PluginsInfo();
    }

    public function allInfo() {
        $name = $this->wp->name( true );

        return [
            'admin_email' => get_option( 'admin_email' ),
            'first_name'  => $name['first_name'],
            'last_name'   => $name['last_name'],
            'ip_address'  => $this->wp->get_user_ip_address(),
            'plugins'     => $this->plugins->plugins(),
            'site_name'   => $this->wp->site_name(),
            'users'       => $this->wp->get_user_counts(),
            'wp'          => $this->wp->get_wp_info(),
            'server'      => $this->system->get_server_info(),
            'post_types'  => $this->wp->post_types(),
            'time_info'   => $this->wp->time_info(),
        ];
    }
}
