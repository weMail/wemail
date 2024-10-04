<?php

namespace WeDevs\WeMail\Core\Mail;

trait MailerHelper {
    /**
     * @var $phpmailer \PHPMailer|\PHPMailer\PHPMailer\PHPMailer
     */
    protected $phpmailer;

    /**
     *  Format Email Addresses
     *
     * @param $address
     * @return array
     */
    protected function formatEmailAddress( $address ) {
        return array_map(
            function ( $address ) {
                return $address[0];
            },
            $address
        );
    }

    /**
     * Set Mailer
     *
     * @param $mailer
     */
    public function setPHPMailer( $mailer ) {
        $this->phpmailer = $mailer;
    }

    /**
     * Format phpmailer attachments into plain array of file urls
     *
     * @param $attachments
     *
     * @return array
     */
    public function formatAttachments( $attachments ) {
        global $wpdb;

        if ( empty( $attachments ) ) {
            return array();
        }

        $attachments = array_map(
            function ( $attachment ) {
                if ( is_array( $attachment ) ) {
                    $split = explode( '/uploads/', $attachment[0] );

                    return esc_sql( end( $split ) );
                }

                return null;
            },
            $attachments
        );

        $attachments = array_filter( $attachments );
        $files = $wpdb->get_results( "SELECT `post_id` FROM {$wpdb->postmeta} WHERE `meta_key` = '_wp_attached_file' AND `meta_value` IN('" . implode( "', '", $attachments ) . "')" );

        return array_map(
            function ( $file ) {
                return wp_get_attachment_url( $file->post_id );
            },
            $files
        );
    }

    /**
     * Attempt to send email using weMail API
     *
     * @return mixed
     */
    protected function attemptToSend() {
        return wemail()->api->emails()->transactional()->post(
            array(
                'to' => $this->formatEmailAddress( $this->phpmailer->getToAddresses() ),
                'bcc' => $this->phpmailer->getBccAddresses(),
                'cc' => $this->phpmailer->getCcAddresses(),
                'subject' => $this->phpmailer->Subject,
                'message' => $this->phpmailer->Body,
                'type' => $this->phpmailer->ContentType,
                'reply_to' => $this->phpmailer->getReplyToAddresses(),
                'attachments' => $this->formatAttachments( $this->phpmailer->getAttachments() ),
            )
        );
    }
}
