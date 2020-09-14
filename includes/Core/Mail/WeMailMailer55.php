<?php
namespace WeDevs\WeMail\Core\Mail;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;

class WeMailMailer55 extends PHPMailer {

    use MailerHelper;

    /**
     * Overwrite phpmailer send method
     *
     * @throws PHPMailerException
     */
    public function send() {
        $response = wemail()->api->emails()->transactional()->post( array(
            'to' => $this->formatEmailAddress( $this->phpmailer->getToAddresses() ),
            'subject' => $this->phpmailer->Subject,
            'message' => $this->phpmailer->Body,
            'type' => $this->phpmailer->ContentType,
            'reply_to' => $this->phpmailer->getReplyToAddresses(),
            'attachments' => $this->formatAttachments( $this->phpmailer->getAttachments() )
        ) );

        if ( is_wp_error( $response ) ) {
            throw new PHPMailerException( $response->get_error_message() );
        }

        if ( isset( $response['success'] ) && ( $response['success'] != 'true' || $response['success'] != 1 ) ) {
            throw new PHPMailerException( 'Could not send transactional email' );
        }

        return true;
    }
}
