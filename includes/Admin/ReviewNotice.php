<?php

namespace WeDevs\WeMail\Admin;

/**
 * Shiw admin notice.
 */
class ReviewNotice {
    private static $instance;

    public static function instance() {
        if ( ! isset( self::$instance ) ) {
            self::$instance = new ReviewNotice();
            self::$instance->boot();
        }

        return self::$instance;
    }

    /**
     * Boot the notice
     */
    private function boot() {
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
    }

    public function enqueue_assets() {
        wp_register_style( 'wemail-admin-notice-style', WEMAIL_ASSETS . '/css/notice.css', false, filemtime( WEMAIL_PATH . '/assets/css/notice.css' ) );
        wp_register_script( 'wemail-admin-notice-script', WEMAIL_ASSETS . '/js/admin-notice.js', [ 'jquery' ], filemtime( WEMAIL_PATH . '/assets/js/admin-notice.js' ), true );

        wp_enqueue_style( 'wemail-admin-notice-style' );
        wp_enqueue_script( 'wemail-admin-notice-script' );
    }

    /**
     * Notice markup to notify to connect site in weMail
     *
     * @return void
     */
    public function connect_review_notice_html() {
        ?>
            <div class="notice wemail-review-notice-flex-container is-dismissible">
                <div class="wemail-connect-notice-content">
                    <h3><?php echo __( 'Review', 'wemail' ); ?></h3>
                    <p><?php echo __( 'You have been using the weMail puglin for over 1 week now. May we ask you to give it a 5-star rating on wordpress?', 'wemail' ); ?></p>
                    <p><?php echo __( 'Sincerely weMail Team.', 'wemail' ); ?></p>
                    <div class="wemail-connect-notice-connect-button" style="margin-bottom: 20px">
                        <form action='' method='post'>
                            <button type="submit" class="button" name="review_reposnse_yes" > Yes, You Deserve It </button>
                            <button type="submit" class="button" name="review_response_later" > Maybe Later </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php
    }

    /**
     * Show notice to connect site in weMail
     *
     * @return bool
     */
    public function connect_review_notice() {
        if ( isset( $_GET['dismiss_wemail_review_notice'] ) && (int) $_GET['dismiss_wemail_review_notice'] === 1 ) {
            update_option( 'wemail_review_notice', 1 );
        }

        $installed_time = get_option('wemail_installed_time');

        $this->handleReviewResponse();

        $this->checkCampaign();

        if ( (int) get_option( 'wemail_review_notice' ) !== 1 ) {
            if ( (time() - $installed_time) / 86400 > 7 || (int)get_option('wemail_sent_campaign_count') >= 3) {
                add_action('admin_notices', [$this, 'connect_review_notice_html']);
            }
        }
    }

    public function review_response($response) {
        update_option( 'wemail_response_for_review_notice', $response );
        update_option( 'wemail_review_notice', 1 );
    }

    /**
     * @return void
     */
    public function checkCampaign()
    {
        if (false === ($value = get_transient('wemail_sent_campaign_count'))
            && (int) get_option('wemail_sent_campaign_count') < 3) {
            $count_campaigns = count(wemail()->api->campaigns()->get()['data']);
            set_transient('wemail_sent_campaign_count', $count_campaigns, 60 * 60 * 2);
            update_option('wemail_sent_campaign_count', $count_campaigns);
        }
    }

    /**
     * @return void
     */
    public function handleReviewResponse()
    {
        if (isset($_POST['review_reposnse_yes'])) {
            $this->review_response('yes');
        }
        if (isset($_POST['review_response_later'])) {
            $this->review_response('later');
        }
    }

}
