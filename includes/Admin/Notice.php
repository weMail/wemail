<?php

namespace WeDevs\WeMail\Admin;

/**
 * Shiw admin notice.
 */
class Notice {
    private static $instance;

    public static function instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new Notice();
            self::$instance->boot();
        }

        return self::$instance;
    }

    /**
     * Boot the notice
     */
    private function boot() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    public function enqueue_assets() {
        wp_register_style( 'wemail-admin-notice-style', WEMAIL_ASSETS . '/css/notice.css', false, filemtime( WEMAIL_PATH . '/assets/css/notice.css' ) );
        wp_register_script( 'wemail-admin-notice-script', WEMAIL_ASSETS . '/js/admin-notice.js', array( 'jquery' ), filemtime( WEMAIL_PATH . '/assets/js/admin-notice.js' ), true );

        wp_enqueue_style( 'wemail-admin-notice-style' );
        wp_enqueue_script( 'wemail-admin-notice-script' );

        wp_localize_script(
            'wemail-admin-notice-script', 'wemail_notice_nonce', array(
                'nonce' => wp_create_nonce( 'wemail_dismiss_notice_nonce' ),
            )
        );
    }

    /**
     * Notice markup to notify to connect site in weMail
     *
     * @return void
     */
    public function connect_notice_html() {
        ?>
            <div class="notice wemail-connect-notice-flex-container is-dismissible">
                <div class="wemail-connect-notice-logo">
                    <img src="<?php echo WEMAIL_ASSETS . '/images/site-connect-notice-logo.svg'; ?>" alt="weMail Logo">
                </div>
                <div class="wemail-connect-notice-content">
                    <h3><?php echo __( 'Connect weMail', 'wemail' ); ?></h3>
                    <p><?php echo __( 'You are one step closer to use weMail. Connect your site to get started with weMail. With weMail, you can send marketing and transactional emails to your audience.', 'wemail' ); ?></p>
                </div>
                <div class="wemail-connect-notice-connect-button">
                    <a class="button" href="<?php echo wemail()->wemail_app . '/connect?email=' . rawurlencode( wp_get_current_user()->user_email ) . '&site_name=' . rawurlencode( get_bloginfo( 'name' ) ) . '&site_url=' . rawurlencode( site_url( '/' ) ) . '&redirect_url=' . rawurlencode( admin_url( 'admin.php?page=wemail' ) ); ?>">Connect</a>
                </div>
            </div>
        <?php
    }

    /**
     * Show notice to connect site in weMail
     *
     * @return void
     */
    public function connect_notice() {
        if ( isset( $_GET['dismiss_connect_notice'] ) && (int) $_GET['dismiss_connect_notice'] === 1 ) {
            if ( isset( $_GET['wemail_dismiss_notice_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['wemail_dismiss_notice_nonce'] ) ), 'wemail_dismiss_notice_nonce' ) ) {
                update_option( 'wemail_site_connection_notice', 1 );
            }
        }
        if ( ! get_option( 'wemail_api_key' ) && (int) get_option( 'wemail_site_connection_notice' ) !== 1 && ! ( isset( $_GET['page'] ) && $_GET['page'] === 'wemail' ) ) {
            add_action( 'admin_notices', array( $this, 'connect_notice_html' ) );
        }
    }
}

