<?php

namespace WeDevs\WeMail\Core\Campaign;

use WeDevs\WeMail\Traits\Hooker;

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
        self::$image_dir = wemail()->wemail_cdn . '/images/content-types';

        $this->add_filter( 'wemail_customizer_content_type_settings_campaign', 'content_type_settings' );
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
            'lists'        => wemail()->lists->items(),
            'segments'     => wemail()->segment->items(),
            'events'       => wemail()->campaign->event->all(),
            'campaign'     => [
                'name'     => '',
                'type'     => 'standard',
                'version'  => WEMAIL_VERSION,
                'lists'    => [],
                'segments' => [],
                'event'    => [
                    'action'          => 'wemail_subscribed_to_list',
                    'value'           => '',
                    'schedule_type'   => 'immediately',
                    'schedule_offset' => 1
                ]
            ]
        ];
    }

    /**
     * i18n strings for campaign editor page
     *
     * i18n for Customizer will add via filter hook at the end
     * of this function
     *
     * @since 1.0.0
     *
     * @return array
     */
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
            'noSegmentSelected'      => __( 'No segment selected', 'wemail' ),
            'currentServerTimeIs'    => __( 'Current server time is', 'wemail' )
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
            'countdown',
            'wooProducts'
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
            'type'       => 'wpPosts',
            'title'      => __( 'WP Posts', 'wemail' ),
            'image'      => self::$image_dir . '/wp.png',
            'default'    => [
                'style' => [
                    'padding' => '15px',
                ],
                'posts' => [
                    [
                        'id' => 0,
                        'image' => wemail()->wemail_cdn . '/images/placeholder-image-full.png',
                        'title' => 'Example post title',
                        'postType' => '',
                        'postStatus' => '',
                        'url' => site_url( '/' ),
                        'content' => 'This is an example of post content',
                        'excerpt' => 'This is an example of post excerpt',
                    ]
                ],
                'layout' => 'i1-tc1',
                'column' => 1,
                'title' => [
                    'container' => [
                        'tag' => 'h2',
                        'style' => [
                            'lineHeight' => '30px',
                            'marginBottom' => '15px'
                        ]
                    ],
                    'style' => [
                        'fontSize' => '22px',
                        'color' => '#333333',
                        'textAlign' => 'left',
                        'fontWeight' => 'normal',
                        'textTransform' => 'none',
                    ],
                ],
                'image' => [
                    'active' => true,
                    'style' => [
                        'width' => '100%',
                        'float' => 'none',
                        'borderWidth' => '0px',
                        'borderStyle' => 'solid',
                        'borderColor' => '#cccccc',
                        'padding' => '0px',
                        'backgroundColor' => '#ffffff',
                        'marginTop' => '0px',
                        'marginRight' => '0px',
                        'marginBottom' => '0px',
                        'marginLeft' => '0px',
                    ]
                ],
                'postContent' => [
                    'length' => 'excerpt',
                    'containerStyle' => [
                        'marginBottom' => '15px',
                    ],
                    'style' => [
                        'fontSize' => '14px',
                        'color' => '#333333',
                        'textAlign' => 'left',
                        'lineHeight' => '20px',
                    ],
                ],
                'readMore' => [
                    'show' => 'display',
                    'style' => [
                        'display' => 'inline-block',
                        'paddingTop' => '0px',
                        'paddingRight' => '0px',
                        'paddingBottom' => '0px',
                        'paddingLeft' => '0px',
                        'fontSize' => '14px',
                        'fontWeight' => 'normal',
                        'lineHeight' => '1',
                        'color' => '#0073aa',
                        'textAlign' => 'center',
                        'textDecoration' => 'underline',
                        'textTransform' => 'none',
                        'backgroundColor' => '',
                        'borderRadius' => '0px',
                        'borderWidth' => '0px',
                        'borderStyle' => 'solid',
                        'borderColor' => '#e5e5e5'
                    ],
                    'text' => __( 'Read More', 'wemail' ),
                    'containerStyle' => [
                        'textAlign' => 'left',
                    ]
                ],
                'divider' => [
                    'display' => 'show',
                    'containerStyle' => [
                        'marginTop' => '15px',
                        'marginBottom' => '15px'
                    ],
                    'style' => [
                        'width' => '570px',
                        'borderTopWidth' => '1px',
                        'borderTopStyle' => 'dashed',
                        'borderTopColor' => '#e5e5e5',
                    ],
                    'useImage' => true,
                    'image' => [
                        'image' => wemail()->wemail_cdn . '/images/dividers/brush-stroke-lite.png',
                        'style' => [
                            'height' => '7px',
                            'width' => '600px',
                        ]
                    ]
                ],
                'author' => [
                    'position' => 'above',
                    'precededBy' => 'Author:',
                ],
                'categories' => [
                    'position' => 'above',
                    'precededBy' => 'Categories:'
                ]
            ],
            'noSettingsTab' => true
        ];
    }
}
