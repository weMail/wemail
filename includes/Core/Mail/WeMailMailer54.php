<?php
namespace WeDevs\WeMail\Core\Mail;

use PHPMailer;

class WeMailMailer54 extends PHPMailer {

    use MailerHelper;

    /**
     * Overwrite phpmailer send method
     *
     * @throws \phpmailerException
     */
    public function send() {
        $response = wemail()->api->emails()->transactional()->post(
            array(
                'to' => $this->formatEmailAddress( $this->phpmailer->getToAddresses() ),
                'subject' => $this->phpmailer->Subject,
                'message' => $this->phpmailer->Body,
                'type' => $this->phpmailer->ContentType,
                'reply_to' => $this->phpmailer->getReplyToAddresses(),
                'attachments' => $this->formatAttachments( $this->phpmailer->getAttachments() ),
            )
        );

        if ( is_wp_error( $response ) ) {
            throw new \phpmailerException( $response->get_error_message() );
        }

        if ( isset( $response['success'] ) && ! wemail_validate_boolean( $response['success'] ) ) {
            throw new \phpmailerException( 'Could not send transactional email' );
        }

        return true;
    }
}
