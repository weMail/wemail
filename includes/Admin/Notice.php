<?php

namespace WeDevs\WeMail\Admin;

/**
 * Shiw admin notice.
 */
class Notice {
    private static $instance;

    public static function instance() {
        if (! isset(self::$instance)) {
            self::$instance = new Notice;
            self::$instance->boot();
        }

        return self::$instance;
    }

    /**
     * Boot the notice
     */
    private function boot() {
        wp_register_style( 'wemail-admin-notice-style', WEMAIL_ASSETS . '/css/notice.css', false, filemtime(WEMAIL_PATH . '/assets/css/notice.css') );
        wp_register_script( 'wemail-admin-notice-script', WEMAIL_ASSETS . '/js/admin-notice.js', ['jquery'], filemtime(WEMAIL_PATH . '/assets/js/admin-notice.js'), true );
    }

    /**
     * Notice markup to notify to connect site in weMail
     *
     * @return void
     */
    public function admin_notice_to_connect_site() {
        ?>
            <div class="notice wemail-connect-notice-flex-container is-dismissible">
                <div class="wemail-connect-notice-logo">
                    <img src="<?php echo WEMAIL_ASSETS . '/images/site-connect-notice-logo.svg' ?>" alt="weMail Logo">
                </div>
                <div class="wemail-connect-notice-content">
                    <h3>Connect weMail</h3>
                    <p>You are one step closer to use weMail. Connect your site to get started with weMail. With weMail, you can send marketing and transactional emails to your audience.</p>
                </div>
                <div class="wemail-connect-notice-connect-button">
                    <a class="button" href="<?php echo wemail()->wemail_app . '/connect?email=' . urlencode(wp_get_current_user()->user_email) . '&site_name=' . urlencode(get_bloginfo('name')) . '&site_url=' . urlencode(site_url( '/' )) . '&redirect_url=' . urlencode(admin_url( 'admin.php?page=wemail' )) ?>">Connect</a>
                </div>
            </div>
        <?php
    }

    /**
     * Show notice to connect site in weMail
     *
     * @return void
     */
    public function show_site_connect_notice() {
        if(isset($_GET['dismiss_connect_notice']) && $_GET['dismiss_connect_notice'] == 1) {
            update_option('dismissed_wemail_site_connection_notice', 1);
        }
        
        if(! get_user_meta(get_current_user_id(), 'wemail_api_key', true) && get_option('dismissed_wemail_site_connection_notice') != 1 && !(isset($_GET['page']) && $_GET['page'] == 'wemail') ) {
            wp_enqueue_style('wemail-admin-notice-style');
            wp_enqueue_script('wemail-admin-notice-script');

            add_action( 'admin_notices', [$this, 'admin_notice_to_connect_site'] );
        }
    }

}