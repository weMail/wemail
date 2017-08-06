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
        $this->action( 'admin_menu', 'register_admin_menu' );
    }

    /**
     * Register admin menu items
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function register_admin_menu() {
        $wemail = add_menu_page( __( 'weMail', 'wemail' ), __( 'weMail', 'wemail' ), 'manage_options', 'wemail', [ $this, 'overview' ], 'dashicons-email-alt' );

        add_submenu_page( 'wemail', __( 'weMail - Overview', 'wemail' ), __( 'Overview', 'wemail' ), 'manage_options', 'wemail', [ $this, 'overview' ] );
        add_submenu_page( 'wemail', __( 'weMail - Campaigns', 'wemail' ), __( 'Campaigns', 'wemail' ), 'manage_options', 'wemail-campaigns', [ $this, 'overview' ] );

        do_action( 'wemail-register-admin-menu' );

        add_submenu_page( 'wemail', __( 'weMail - Subscribers', 'wemail' ), __( 'Subscribers', 'wemail' ), 'manage_options', 'wemail-subscribers', [ $this, 'overview' ] );
        add_submenu_page( 'wemail', __( 'weMail - Forms', 'wemail' ), __( 'Forms', 'wemail' ), 'manage_options', 'wemail-forms', [ $this, 'overview' ] );
        add_submenu_page( 'wemail', __( 'weMail - Lists', 'wemail' ), __( 'Lists', 'wemail' ), 'manage_options', 'wemail-lists', [ $this, 'overview' ] );
        add_submenu_page( 'wemail', __( 'weMail - Settings', 'wemail' ), __( 'Settings', 'wemail' ), 'manage_options', 'wemail-settings', [ $this, 'overview' ] );

        $this->action( 'admin_print_styles-' . $wemail, 'enqueue_styles' );
        $this->action( 'admin_print_scripts-' . $wemail, 'enqueue_scripts' );
    }

    public function enqueue_styles() {
        do_action( 'wemail-admin-enqueue-styles' );
    }

    public function enqueue_scripts() {
        wp_enqueue_script( 'vue' );
        wp_enqueue_script( 'vuex' );
        wp_enqueue_script( 'vue-router' );

        do_action( 'before-wemail-admin-enqueue-scripts' );

        wp_enqueue_script( 'wemail', WEMAIL_ASSETS . '/js/wemail.js', ['jquery', 'vue', 'vuex', 'vue-router'], WEMAIL_VERSION, true );

        do_action( 'wemail-admin-enqueue-scripts' );
    }

    public function overview() {
        echo 'hello world';
    }
}
