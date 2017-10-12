<?php

namespace WeDevs\WeMail\Modules\Campaign;

use WeDevs\WeMail\Framework\Traits\Hooker;

class Editor {

    use Hooker;

    /**
     * Content type images directory
     *
     * @since 1.0.0
     *
     * @var string
     */
    public static $image_dir;

    /**
     * Class contructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        self::$image_dir = WEMAIL_ASSETS . '/images/content-types';

        $this->add_filter( 'wemail_customizer_content_type_settings_campaign', 'content_type_settings' );
        $this->add_filter( 'wemail_customizer_i18n_campaign', 'customizer_i18n' );
    }

    /**
     * Setup step route data
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_setup_data() {
        return [
            'i18n'         => $this->i18n(),
            'lists'        => wemail()->lists->all(),
            'segments'     => wemail()->segment->all(),
            'events'       => wemail()->campaign->event->all(),
            'campaign'     => [
                'name'     => '',
                'type'     => 'standard',
                'lists'    => [],
                'segments' => [],
                'event'    => [
                    'action'          => 'wemail_subscribed_to_list',
                    'option_value'    => '',
                    'schedule_type'   => 'immediately',
                    'schedule_offset' => 1
                ]
            ]
        ];
    }

    private function i18n() {
        $i18n = [
            'createCampaign'         => __( 'Create Campaign', 'wemail' ),
            'editCampaign'           => __( 'Edit Campaign', 'wemail' ),
            'campaignName'           => __( 'Campaign Name', 'wemail' ),
            'campaignNameHint'       => __( 'Enter a name to help you remember what this campaign is all about. Only you will see this.', 'wemail' ),
            'campaignType'           => __( 'Campaign Type', 'wemail' ),
            'standard'               => __( 'Standard', 'wemail' ),
            'automatic'              => __( 'Automatic', 'wemail' ),
            'subscribers'            => __( 'Subscribers', 'wemail' ),
            'lists'                  => __( 'Lists', 'wemail' ),
            'segments'               => __( 'Segments', 'wemail' ),
            'selectLists'            => __( 'Select Lists', 'wemal' ),
            'selectSegments'         => __( 'Select Segments', 'wemal' ),
            'setup'                  => __( 'Setup', 'wemail' ),
            'template'               => __( 'Template', 'wemail' ),
            'design'                 => __( 'Design', 'wemail' ),
            'send'                   => __( 'Send', 'wemail' ),
            'next'                   => __( 'Next', 'wemail' ),
            'previous'               => __( 'Previous', 'wemail' ),
            'automaticallySend'      => __( 'Automatically Send', 'wemail' ),
            'noOptionFoundForAction' => __( 'no option found for this action', 'wemail' ),
            'immediately'            => __( 'Immediately', 'wemail' ),
            'hoursAfter'             => __( 'hour(s) after', 'wemail' ),
            'daysAfter'              => __( 'day(s) after', 'wemail' ),
            'weeksAfter'             => __( 'week(s) after', 'wemail' ),
            'templates'              => __( 'Templates', 'wemail' ),
            'all'                    => __( 'All', 'wemail' ),
            'chooseTemplateCategory' => __( 'Choose a template category', 'wemail' ),
            'templateNotFound'       => __( 'Template not found', 'wemail' ),
            'previewTemplate'        => __( 'Preview Template', 'wemail' ),
            'myTemplates'            => __( 'My Templates', 'wemail' ),
            'noTemplateFound'        => __( 'No template found', 'wemail' ),
            'searchTemplate'         => __( 'Search Templates', 'wemail' ),
            'useThisTemplate'        => __( 'Use this template', 'wemail' ),
            'preview'                => __( 'Preview', 'wemail' ),
            'selectTemplate'         => __( 'Select Template', 'wemail' ),
            'close'                  => __( 'Close', 'wemail' ),
            'myTemplates'            => __( 'My Templates', 'wemail' ),
            'emailSubject'           => __( 'Email Subject', 'wemail' ),
            'preHeader'              => __( 'Preheader', 'wemail' ),
            'preHeaderHint'          => __( 'A preheader is the short summary text that follows the subject line when an email is viewed in the inbox.', 'wemail' ),
            'sender'                 => __( 'Sender', 'wemail' ),
            'senderHint'             => __( 'Name & email of yourself or your company.', 'wemail' ),
            'replyTo'                => __( 'Reply To', 'wemail' ),
            'replyToHint'            => __( 'When the subscribers hit "reply" this is who will receive their emails.', 'wemail' ),
            'name'                   => __( 'Name', 'wemail' ),
            'email'                  => __( 'Email', 'wemail' ),
            'scheduleCampaign'       => __( 'Schedule Campaign', 'wemail' ),
            'yesScheduleIt'          => __( 'Yes schedule it', 'wemail' ),
            'googleAnalyticCampaign' => __( 'Google Analytic Campaign', 'wemail' ),
            'utmHint'                => __( 'For example "New year sale"', 'wemail' ),
            'campaignSummery'        => __( 'Campaign Summery', 'wemail' ),
            'thisFieldIsRequired'    => __( 'This field is required', 'wemail' ),
            'charactersRemaining'    => __( 'characters remaining', 'wemail' ),
            'invalidEmailAddress'    => __( 'Invalid email address', 'wemail' ),
            'noListSelected'         => __( 'No list selected', 'wemail' ),
            'noSegmentSelected'      => __( 'No segment selected', 'wemail' )
        ];

        /**
         * i18n strings for email campaign editor pages
         *
         * @since 1.0.0
         *
         * @param array $i18n
         */
        return apply_filters( 'wemail_campaign_editor_i18n', $i18n );
    }

    /**
     * Data for email customizer
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function get_customizer_data() {
        $content_types = [
            'text',
            'image',
            'imageCaption',
            'socialFollow',
            'button',
            'divider',
            'video',
            'footer',
            'wpPosts',
            'wpLatestContents',
        ];

        return wemail()->customizer->get( 'campaign', $content_types );
    }

    /**
     * Content types for email campaign
     *
     * @since 1.0.0
     *
     * @param array $settings
     *
     * @return array
     */
    public function content_type_settings( $settings ) {
        $additional_types = [
            'wpPosts'          => self::wp_posts(),
            'wpLatestContents' => self::wp_latest_contents()
        ];

        $settings = array_merge( $settings, $additional_types );

        return $settings;
    }

    /**
     * WP Posts type content
     *
     * @since 1.0.0
     *
     * @return array
     */
    public static function wp_posts() {
        return [
            'type' => 'wpPosts',
            'image'      => self::$image_dir . '/wp.png',
            'default'    => [
                'style'      => [
                    'backgroundColor' => '#ffffff',
                    'paddingTop'      => '15px',
                    'paddingBottom'   => '15px',
                    'paddingLeft'     => '15px',
                    'paddingRight'    => '15px',
                    'borderWidth'     => '0px',
                    'borderStyle'     => 'solid',
                    'borderColor'     => '#e5e5e5'
                ],
                'twoColumns' => false,
                'texts'      => [
                    sprintf( '<p>%s</p>', __( 'dkls', 'wemail' ) ),
                    sprintf( '<p>%s</p>', __( 'dkls', 'wemail' ) )
                ],

                'valign'     => 'top',
            ]
        ];
    }

    /**
     * WP Latest Contents type content
     *
     * @since 1.0.0
     *
     * @return array
     */
    public static function wp_latest_contents() {
        return [
            'image'      => self::$image_dir . '/wp-latest.png',
            'default'    => [
                'style'      => [
                    'backgroundColor' => '#ffffff',
                    'paddingTop'      => '15px',
                    'paddingBottom'   => '15px',
                    'paddingLeft'     => '15px',
                    'paddingRight'    => '15px',
                    'borderWidth'     => '0px',
                    'borderStyle'     => 'solid',
                    'borderColor'     => '#e5e5e5'
                ],
                'twoColumns' => false,
                'texts'      => [
                    sprintf( '<p>%s</p>', __( 'dkls', 'wemail' ) ),
                    sprintf( '<p>%s</p>', __( 'dkls', 'wemail' ) )
                ],

                'valign'     => 'top',
            ]
        ];
    }

    /**
     * i18n strings for email customizer
     *
     * @since 1.0.0
     *
     * @param array $i18n
     *
     * @return array
     */
    public function customizer_i18n( $i18n ) {
        return array_merge( $i18n, [
            'wpPosts'           => __( 'WP Posts', 'wemail' ),
            'wpLatestContents'  => __( 'Latest Contents', 'wemail' ),
        ] );
    }

}
