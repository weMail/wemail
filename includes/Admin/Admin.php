<?php

namespace WeDevs\WeMail\Admin;

use WeDevs\WeMail\Admin\Menu;
use WeDevs\WeMail\Admin\Scripts;
use WeDevs\WeMail\Traits\Hooker;

class Admin {

    use Hooker;

    public function __construct() {
        $this->add_action( 'admin_init', 'redirect_after_activation', 9999 );
        $this->includes();
        $admin_notice = Notice::instance();
        $admin_notice->connect_notice();
    }

    private function includes() {
        new Scripts();
        new Menu();
        new FormPreview();

        if ( current_user_can( 'edit_posts' ) && wemail_validate_boolean( get_user_option( 'rich_editing' ) ) ) {
            new Shortcode();
        }
    }

    public function remove_admin_notice() {
        remove_all_actions( 'admin_notices' );
    }

    /**
     * Redirect to wemail setup page after plugin installation
     *
     * If the setup isn't done, it'll be redirected to the wemail page,
     * also that redirects to the authentication page.
     *
     * @return void
     */
    public function redirect_after_activation() {
        if ( ! get_transient( 'wemail_activation_redirect' ) || wemail()->user->api_key ) {
            return;
        }

        delete_transient( 'wemail_activation_redirect' );

        // Only do this for single site installs.
        if ( is_network_admin() || isset( $_GET['activate-multi'] ) ) {
            return;
        }

        wp_safe_redirect( admin_url( 'admin.php?page=wemail' ) );
        exit;
    }

}
