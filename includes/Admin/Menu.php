<?php

namespace WeDevs\WeMail\Admin;

use WeDevs\WeMail\Traits\Hooker;

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
        $disabled = get_user_meta( get_current_user_id(), 'wemail_disable_user_access' );

        if ( ! $disabled ) {
            $this->add_action( 'admin_menu', 'register_admin_menu' );
            $this->add_action( 'admin_print_styles', 'add_menu_icon_style' );
        }
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

        if ( current_user_can( 'manage_options' ) || wemail()->user->can( 'view_wemail' ) ) {
            $capability = 'read';

            /**
             * Filter weMail menu position
             *
             * @since 1.0.0
             *
             * @var int
             */
            $menu_position = apply_filters( 'wemail_main_menu_position', 56 );

            $wemail = add_menu_page( __( 'weMail', 'wemail' ), __( 'weMail', 'wemail' ), $capability, 'wemail', [ $this, 'admin_view' ], 'data:image/svg+xml;base64,' . base64_encode('<svg viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><g fill="none"><path d="M10.002 13.806l-8.393-4.7a.407.407 0 00-.402.006.421.421 0 00-.207.351v7.721C1 18.187 1.799 19 2.784 19h14.432c.473 0 .927-.191 1.261-.532.335-.34.523-.802.523-1.284V9.408a.421.421 0 00-.208-.353.407.407 0 00-.405-.002l-8.385 4.753z"/><path d="M10.035 12l7.752-4.263a.404.404 0 00.071-.664c-1.804-1.545-4.937-4.167-6.61-5.618a1.888 1.888 0 00-2.434-.021c-1.75 1.464-4.84 4.145-6.671 5.694a.404.404 0 00.07.664L10.035 12z"/></g></svg>'), $menu_position );

            // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
            $submenu['wemail'] = apply_filters( 'wemail_admin_submenu', [], $capability );

            $this->add_action( 'admin_print_styles-' . $wemail, 'enqueue_styles' );
            $this->add_action( 'admin_print_scripts-' . $wemail, 'enqueue_scripts' );
        }
    }

    /**
     * Enqueue weMail admin css hook
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function enqueue_styles() {
        do_action( 'wemail_admin_enqueue_styles' );
    }

    /**
     * Enqueue weMail admin js hook
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function enqueue_scripts() {
        do_action( 'wemail_admin_enqueue_scripts' );
    }

    /**
     * Admin page view
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function admin_view() {
        include_once WEMAIL_VIEWS . '/admin.php';
    }

    public function add_menu_icon_style() {
        ?>
            <style>
                .menu-top.toplevel_page_wemail .wp-menu-image img {
                    width: 20px;
                    padding-top: 10px !important;
                }
            </style>
        <?php
    }
}
