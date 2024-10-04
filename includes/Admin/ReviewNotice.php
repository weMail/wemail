<?php

namespace WeDevs\WeMail\Admin;

use WeDevs\WeMail\Traits\Singleton;

class ReviewNotice {
    use Singleton;

    public $day_count;

    public $campaign_count;

    public $time_based_review;

    /**
     * Boot the notice
     */
    private function boot() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    public function enqueue_assets() {
        wp_enqueue_style( 'wemail-review-notice-style', WEMAIL_ASSETS . '/css/review-notice.css', false, WEMAIL_VERSION );
        wp_enqueue_script( 'wemail-review-notice-script', WEMAIL_ASSETS . '/js/admin-review-notice.js', array( 'jquery' ), WEMAIL_VERSION, true );
    }

    /**
     * Notice markup to notify to connect site in weMail
     *
     * @return void
     */
    public function connect_review_notice_html() {
		?>
        <div class="notice wemail-review-notice-flex-container is-dismissible review-time-background-image" data-nonce="<?php echo wp_create_nonce( '_review_nonce' ); ?>" >
            <div class="wemail-connect-notice-content">
                <div class="wemail-connect-notice-content">
                    <h3>
                        <?php
                        echo __( 'Great! ', 'wemail' );
                        if ( $this->time_based_review ) {
                            /* translators: %d is replaced with the number of days */
                            printf( __( 'You are using weMail for %s absolutely free', 'wemail' ), $this->day_count > 31 ? 'more than 1 month' : intval( $this->day_count ) . ' days' );
                        } else {
                            /* translators: %d is replaced with the number of campaigns */
                            printf( __( 'You sent %d campaigns using weMail successfully.', 'wemail' ), $this->campaign_count );
                        }
                        ?>
                    </h3>
                    <p>
                        <?php
                        echo __(
                            'Don\'t worry, we are not asking you to buy the pro plan. But would you give us a great review, if you will? We put a lot of
                            hard work to develop it and make it better every day. It would motivate the team a lot! Do you think it\'s fair?', 'wemail'
                        );
                        ?>
                    </p>
                    <div class="wemail-reivew-notice-connect-button" style="margin-bottom: 10px: margin top: 10px">
                        <form method='post'>
                            <button type="submit" class="button review_reposnse_yes" id="review_reposnse_yes" name="review_reposnse_yes">
                                <?php echo __( 'Yes, Absolutely', 'wemail' ); ?>
                            </button>
                            <button type="submit" class="button response-button" id="review_reposnse_later" name="review_response_later">
                                <?php echo __( 'Ask me later', 'wemail' ); ?>
                            </button>
                            <button type="submit" class="response-button" id="review_reposnse_no" name="review_response_no">
                                <u> <?php echo __( 'No, I\'m just taking, not giving', 'wemail' ); ?> </u>
                            </button>
                            <?php wp_nonce_field( '_review_nonce', 'review_nonce' ); ?>
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
        if ( ! isset( $_GET['page'] ) || $_GET['page'] !== 'wemail' ) {
            return;
        }

        if ( (int) get_option( 'wemail_review_notice' ) === 1 ) {
            return;
        }

        if ( isset( $_GET['dismiss_wemail_review_notice'] ) && (int) $_GET['dismiss_wemail_review_notice'] === 1 ) {
            if ( ! isset( $_GET['review_nonce'] ) ) {
                wp_die( __( 'Nonce not found!', 'wemail' ) );
            }

            if ( ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['review_nonce'] ) ), '_review_nonce' ) ) {
                wp_die( __( 'Invalid nonce!', 'wemail' ) );
            }

            update_option( 'wemail_review_notice', 1 );
            return;
		}

        $this->handle_review_response();

        $this->check_campaign();

        $installed_time = get_option( 'wemail_installed_time', time() );
        $sent_campaigns = (int) get_option( 'wemail_sent_campaign_count', 0 );

        $this->time_based_review = ( time() - $installed_time ) / 86400 > 7;
        $this->day_count = (int) ( time() - $installed_time ) / 86400;
        $this->campaign_count = $sent_campaigns;

        if ( $this->time_based_review || $sent_campaigns >= 3 ) {
            add_action( 'admin_notices', array( $this, 'connect_review_notice_html' ) );
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
        if ( get_transient( 'wemail_sent_campaign_count' ) !== false ) {
            return;
        }

        $response = wemail()->api->campaigns()->query(
            array(
				'statuses' => array( 'completed' ),
			)
        )->get();

        if ( is_wp_error( $response ) || ! isset( $response['meta'] ) ) {
            set_transient( 'wemail_sent_campaign_count', 1, 60 * 60 * 2 );
            return;
        }

        $count_campaigns = (int) $response['meta']['total'];
        set_transient( 'wemail_sent_campaign_count', $count_campaigns, 60 * 60 * 2 );
        update_option( 'wemail_sent_campaign_count', $count_campaigns );
    }

    /**
     * @return void
     */
    public function handle_review_response() {
        if ( isset( $_POST['submit'] ) && ! isset( $_POST['review_nonce'] ) ) {
            wp_die( __( 'Nonce Not Found', 'wemail' ) );
        }

        if ( isset( $_POST['submit'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['review_nonce'] ) ), '_review_nonce' ) ) {
            wp_die( __( 'Security check', 'wemail' ) );
        }

        if ( isset( $_POST['review_reposnse_yes'] ) ) {
            $this->review_response( 'yes' );
            wp_redirect( 'https://wordpress.org/support/plugin/wemail/reviews/?filter=5' );
            exit;
		} elseif ( isset( $_POST['review_response_later'] ) ) {
			$this->review_response( 'later' );
		} elseif ( isset( $_POST['review_response_no'] ) ) {
            $this->review_response( 'no' );
        }
    }
}
