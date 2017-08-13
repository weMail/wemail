<?php
namespace WeDevs\WeMail\Admin;

use WeDevs\WeMail\Framework\Traits\Hooker;

class Menu {

    use Hooker;

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->add_action( 'admin_menu', 'register_admin_menu' );
    }

    /**
     * Register admin menu items
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function register_admin_menu() {
        global $submenu;

        $capability = 'manage_options';

        $wemail = add_menu_page( __( 'weMail', 'wemail' ), __( 'weMail', 'wemail' ), $capability, 'wemail', [ $this, 'admin_view' ], 'dashicons-email-alt' );

        $wemail_submenu = apply_filters( 'wemail-admin-submenu', [], $capability );

        $submenu['wemail'] = $wemail_submenu;


        $this->add_action( 'admin_print_styles-' . $wemail, 'enqueue_styles' );
        $this->add_action( 'admin_print_scripts-' . $wemail, 'enqueue_scripts' );
    }

    public function enqueue_styles() {
        do_action( 'wemail-admin-enqueue-styles' );
    }

    public function enqueue_scripts() {
        do_action( 'wemail-admin-enqueue-scripts' );
    }

    public function admin_view() {
        include_once WEMAIL_VIEWS . '/admin.php';
    }
}
