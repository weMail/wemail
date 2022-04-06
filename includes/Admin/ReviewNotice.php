<?php

namespace WeDevs\WeMail\Admin;

/**
 * Shiw admin notice.
 */
class ReviewNotice {

    private static $instance;
    public $time_based_review;
    public $day_count;
    public $campaign_count;

    public static function instance() {         if ( ! isset( self::$instance ) ) {
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
        wp_register_style( 'wemail-review-notice-style', WEMAIL_ASSETS . '/css/reviewNotice.css', false, filemtime( WEMAIL_PATH . '/assets/css/reviewNotice.css' ) );
        wp_register_script( 'wemail-review-notice-script', WEMAIL_ASSETS . '/js/admin-review-notice.js', [ 'jquery' ], filemtime( WEMAIL_PATH . '/assets/js/admin-review-notice.js' ), true );

        wp_enqueue_style( 'wemail-review-notice-style' );
        wp_enqueue_script( 'wemail-review-notice-script' );
    }

    /**
     * Notice markup to notify to connect site in weMail
     *
     * @return void
     */
    public function connect_review_notice_html() {         ?>
        <div class="notice wemail-review-notice-flex-container is-dismissible">
            <div class="wemail-review-notice-logo">
                <img src="
                <?php
                if ( $this->time_based_review ) {
                    echo WEMAIL_ASSETS . '/images/time_based_review.png';
				} else {
					echo WEMAIL_ASSETS . '/images/campaign_based_review.png';
				}
                ?>
                " alt="weMail Notice Logo">
            </div>
            <div class="wemail-connect-notice-content">
                <h3>
                <?php
                echo __( 'Great! ', 'wemail' );
                if ( $this->time_based_review ) {
                    /* translators: %d is replaced with the number of days */
                    printf( __( 'You are using weMail for %d days-absolutely free', 'wemail' ), $this->day_count );
                } else {
                    /* translators: %d is replaced with the number of campaigns */
                    printf( __( 'You sent %d campaigns usign weMail successfully.', 'wemail' ), $this->campaign_count );
                }
                ?>
                </h3>
                <p>
                <?php
                echo __(
                    'May we ask for a 5 start rating on WordPress. We put a lot of hard work to develop it and make it better every day.
                    It would motivate us a lot.', 'wemail'
                );
				?>
                </p>
                <div class="wemail-reivew-notice-connect-button" style="margin-bottom: 20px">
                    <form action='' method='post'>
                        <button type="submit" class="button" name="review_reposnse_yes">
                                Yes, Absolutely
                        </button>
                        <button type="submit" class="response-button" name="review_response_later">  <u> Ask me later </u> </button>
                        <button type="submit" class="response-button" name="review_response_no"> <u> No, not good enough </u> </button>
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
    public function connect_review_notice() {         if ( isset( $_GET['dismiss_wemail_review_notice'] ) && (int) $_GET['dismiss_wemail_review_notice'] === 1 ) {
            update_option( 'wemail_review_notice', 1 );
	}

        $installed_time = get_option( 'wemail_installed_time' );

        $this->handleReviewResponse();

        $this->checkCampaign();

	if ( (int) get_option( 'wemail_review_notice' ) !== 1 ) {
        $this->time_based_review = ( time() - $installed_time ) / 86400 > 7;
        $this->day_count = (int) ( time() - $installed_time ) / 86400;
        $this->campaign_count = (int) get_option( 'wemail_sent_campaign_count' );
        $campaign_based_review = (int) get_option( 'wemail_sent_campaign_count' ) >= 3;
        if ( $this->time_based_review || $campaign_based_review ) {
			add_action( 'admin_notices', [ $this, 'connect_review_notice_html' ] );
		}
	}
    }

    public function review_response( $response ) {
        update_option( 'wemail_response_for_review_notice', $response );
        update_option( 'wemail_review_notice', 1 );
    }

    /**
     * @return void
     */
    public function checkCampaign() {
        if ( (int) get_option( 'wemail_sent_campaign_count' ) < 3 && get_transient( 'wemail_sent_campaign_count' ) === false ) {
            $count_campaigns = count( wemail()->api->campaigns()->get()['data'] );
            set_transient( 'wemail_sent_campaign_count', $count_campaigns, 60 * 60 * 2 );
            update_option( 'wemail_sent_campaign_count', $count_campaigns );
		}
    }

    /**
     * @return void
     */
    public function handleReviewResponse() {
        // phpcs:ignore
        if ( isset( $_POST['review_reposnse_yes'] ) ) {
            $this->review_response( 'yes' );
            header( 'Location:https://wordpress.org/support/plugin/wemail/reviews/#new-post' );
		}
        // phpcs:ignore
		if ( isset( $_POST['review_response_later'] ) ) {
			$this->review_response( 'later' );
		}
    }

}
