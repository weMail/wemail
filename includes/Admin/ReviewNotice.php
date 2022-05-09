<?php

namespace WeDevs\WeMail\Admin;
use WeDevs\WeMail\Traits\Singleton;

/**
 * Shiw admin notice.
 */
class ReviewNotice {
    use Singleton;

    public $time_based_review;
    public $day_count;
    public $campaign_count;

    /**
     * Boot the notice
     */
    private function boot() {
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_assets' ] );
    }

    public function enqueue_assets() {
        wp_enqueue_style( 'wemail-review-notice-style', WEMAIL_ASSETS . '/css/review-notice.css', false, WEMAIL_VERSION);
        wp_enqueue_script( 'wemail-review-notice-script', WEMAIL_ASSETS . '/js/admin-review-notice.js', [ 'jquery' ], WEMAIL_VERSION );
    }

    /**
     * Notice markup to notify to connect site in weMail
     *
     * @return void
     */
    public function connect_review_notice_html() {         ?>
        <div class="notice wemail-review-notice-flex-container is-dismissible review-time-background-image" >
            <div class="wemail-connect-notice-content">
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
                            'Don’t worry, we are not asking you to buy the pro plan. But if you give us a great review, if you will. We put a lot of
                            hard work to develop it and make it better every day. It would motivate the team a lot! Do you think it’s fair?', 'wemail'
                        );
                        ?>
                    </p>
                    <div class="wemail-reivew-notice-connect-button" style="margin-bottom: 10px: margin top: 10px">
                        <form action='' method='post'>
                            <button type="submit" class="button review_reposnse_yes" id="review_reposnse_yes" name="review_reposnse_yes">
                                <?php echo __('Yes, Absolutely'); ?>
                            </button>
                            <button type="submit" class="button response-button" id="review_reposnse_later" name="review_response_later">
                                <?php echo __('Ask me later'); ?>
                            </button>
                            <button type="submit" class="response-button" id="review_reposnse_no" name="review_response_no">
                                <u> <?php echo __('No, I\'m just taking, not giving') ?> </u>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Show notice to connect site in weMail
     *
     * @return void
     */
    public function connect_review_notice() {
        if ( isset( $_GET['dismiss_wemail_review_notice'] ) && (int) $_GET['dismiss_wemail_review_notice'] === 1 ) {
            update_option( 'wemail_review_notice', 1 );
	}

        $installed_time = get_option( 'wemail_installed_time' );

        $this->handle_review_response();

        $this->check_campaign();

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
    public function check_campaign() {
        $campaigns = wemail()->api->campaigns()->get();
            if ( is_array($campaigns) && !empty($campaigns['data']) && (int) get_option( 'wemail_sent_campaign_count' ) < 3 && get_transient( 'wemail_sent_campaign_count' ) === false ) {
            $count_campaigns = count( $campaigns['data'] );
            set_transient( 'wemail_sent_campaign_count', $count_campaigns, 60 * 60 * 2 );
            update_option( 'wemail_sent_campaign_count', $count_campaigns );
		}
    }

    /**
     * @return void
     */
    public function handle_review_response() {
        $nonce = wp_create_nonce('wp_rest');

        if ( wp_verify_nonce($nonce, 'wp_rest') && isset( $_POST['review_reposnse_yes'] ) ) {
            $this->review_response( 'yes' );
            header( 'Location:https://wordpress.org/support/plugin/wemail/reviews/#new-post' );
		}

		if ( wp_verify_nonce($nonce, 'wp_rest') && isset( $_POST['review_response_later'] ) ) {
			$this->review_response( 'later' );
		}

        if ( wp_verify_nonce($nonce, 'wp_rest') && isset( $_POST['review_response_no'] ) ) {
            $this->review_response( 'no' );
        }
    }

}
