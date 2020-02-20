<?php
namespace WeDevs\WeMail\Core\Mail;

use WeDevs\WeMail\Traits\Hooker;

class Hooks {

    use Hooker;

    /**
     * Hooks constructor.
     */
    public function __construct() {
        $this->add_action( 'phpmailer_init', 'handle_transactional_emails' );
    }

    /**
     * Replace phpmailer instance
     *
     * @param $phpmailer
     * @return void
     */
    public function handle_transactional_emails( &$phpmailer ) {
        if ( ! get_option( 'wemail_transactional_emails', false ) ) {
            return;
        }

        $mailer = new WeMailMailer();
        $mailer->setPHPMailer(clone $phpmailer);

        $phpmailer = $mailer;
    }
}
