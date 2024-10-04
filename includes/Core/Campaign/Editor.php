<?php

namespace WeDevs\WeMail\Core\Campaign;

use WeDevs\WeMail\Traits\Hooker;

class Editor {

    use Hooker;

    /**
     * Default content types
     *
     * @var string[] $default_content_types
     */
    protected $default_content_types = array(
        'text',
        'image',
        'imageCaption',
        'socialFollow',
        'button',
        'divider',
        'video',
        'footer',
        'countdown',
        'wooProducts',
        'giphy',
    );

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
        return array(
            'lists'        => wemail()->lists->items(),
            'segments'     => wemail()->segment->items(),
            'events'       => wemail()->campaign->event->all(),
            'campaign'     => array(
                'name'     => '',
                'type'     => 'standard',
                'version'  => WEMAIL_VERSION,
                'lists'    => array(),
                'segments' => array(),
                'event'    => array(
                    'action'          => 'wemail_subscribed_to_list',
                    'value'           => '',
                    'schedule_type'   => 'immediately',
                    'schedule_offset' => 1,
                ),
            ),
        );
    }

    /**
     * I18n strings for campaign editor page
     *
     * I18n for Customizer will add via filter hook at the end
     * of this function
     *
     * @since 1.0.0
     *
     * @return array
     */
    private function i18n() {
        $i18n = array(
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
            'selectLists'            => __( 'Select Lists', 'wemail' ),
            'selectSegments'         => __( 'Select Segments', 'wemail' ),
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
            'currentServerTimeIs'    => __( 'Current server time is', 'wemail' ),
        );

        /**
         * I18n strings for email campaign editor pages
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
    public function get_customizer_data( $context ) {
        switch ( $context ) {
            case 'campaign':
                $this->default_content_types[] = 'wpPosts';
                break;
            case 'rss':
                $this->default_content_types[] = 'rss';
                break;
        }

        return wemail()->customizer->get( 'campaign', $this->default_content_types );
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
        $additional_types = array(
            'wpPosts'          => self::wp_posts(),
            'rss'              => self::rss(),
        );

        return array_merge( $settings, $additional_types );
    }

    /**
     * WP Posts type content
     *
     * @since 1.0.0
     *
     * @return array
     */
    public static function wp_posts() {
        return array(
            'type'       => 'wpPosts',
            'title'      => __( 'WP Posts', 'wemail' ),
            'image'      => self::$image_dir . '/wp.svg',
            'default'    => array(
                'containerStyle' => array(
                    'padding' => '25px',
                    'marginBottom' => '0px',
                    'backgroundColor' => '#ffffff',
                ),
                'posts' => array(),
                'layout' => 'list',
                'post_per_row' => 2,
                'title' => array(
                    'style' => array(
                        'fontSize' => '30px',
                        'color' => '#333333',
                        'textAlign' => 'left',
                        'fontWeight' => 'bold',
                        'textTransform' => 'none',
                        'lineHeight' => '50px',
                        'marginBottom' => '15px',
                    ),
                ),
                'image' => array(
                    'display' => 'show',
                    'style' => array(
                        'max-width' => '100%',
                        'float' => 'none',
                        'borderWidth' => '0px',
                        'borderStyle' => 'solid',
                        'borderColor' => '#cccccc',
                        'padding' => '0px',
                        'backgroundColor' => '#ffffff',
                        'marginTop' => '0px',
                        'marginRight' => '0px',
                        'marginBottom' => '10px',
                        'marginLeft' => '0px',
                    ),
                    'containerStyle' => array(
                        'textAlign' => 'left',
                    ),
                ),
                'content' => array(
                    'display' => 'show',
                    'content_format' => 'excerpt',
                    'excerpt_length' => 55,

                    'style' => array(
                        'fontSize' => '14px',
                        'color' => '#556271',
                        'textAlign' => 'left',
                        'lineHeight' => '20px',
                        'marginBottom' => '15px',
                    ),
                ),
                'readMore' => array(
                    'display' => 'show',
                    'style' => array(
                        'display' => 'inline-block',
                        'paddingTop' => '10px',
                        'paddingRight' => '15px',
                        'paddingBottom' => '10px',
                        'paddingLeft' => '15px',
                        'fontSize' => '14px',
                        'fontWeight' => 'normal',
                        'lineHeight' => '1',
                        'color' => '#fff',
                        'textDecoration' => 'none',
                        'textTransform' => 'none',
                        'backgroundColor' => '#0073aa',
                        'borderRadius' => '4px',
                        'borderWidth' => '0px',
                        'borderStyle' => 'solid',
                        'borderColor' => '#e5e5e5',
                        'marginBottom' => '30px',
                    ),
                    'text' => __( 'Read More', 'wemail' ),
                    'containerStyle' => array(
                        'textAlign' => 'left',
                    ),
                ),
                'meta' => array(
                    'display' => 'show',
                    'separator' => ' | ',
                    'fields' => array(
                        'author' => array(
                            'text' => __( 'Author: ', 'wemail' ),
                            'display' => 'show',
                        ),
                        'categories' => array(
                            'text' => __( 'Categories: ', 'wemail' ),
                            'display' => 'show',
                        ),
                        'tags' => array(
                            'text' => __( 'Tags: ', 'wemail' ),
                            'display' => 'show',
                        ),
                        'postDate' => array(
                            'text' => __( 'Post Date: ', 'wemail' ),
                            'display' => 'show',
                        ),
                    ),
                    'style' => array(
                        'fontSize' => '12px',
                        'color' => '#556271',
                        'textAlign' => 'left',
                        'lineHeight' => '20px',
                        'marginBottom' => '15px',
                        'fontStyle' => 'normal',
                    ),
                ),
            ),
        );
    }

    public static function rss() {
        return array(
            'type' => 'rss',
            'title' => __( 'Latest Posts', 'wemail' ),
            'image' => self::$image_dir . '/wp.svg',
            'noSettingsTab' => true,
            'default' => array(
                'source' => 'wp',
                'payload' => array(
                    'post_type' => 'post',
                    'tag_id'  => null,
                    'category_id'  => null,
                    'limit' => 5,
                ),
                'containerStyle' => array(
                    'padding' => '25px 25px 25px 25px',
                    'marginBottom' => '0px',
                    'background' => '#ffffff',
                ),
                'image' => array(
                    'display' => true,
                    'style' => array(
                        'max-width'         => '100%',
                        'border'            => '0px solid #cccccc',
                        'borderRadius'      => '0px',
                        'padding'           => '0px 0px 0px 0px',
                        'background'        => '#ffffff',
                        'margin'            => '0px 0px 0px 0px',
                    ),
                    'containerStyle' => array(
                        'textAlign' => 'left',
                        'display'   => 'block',
                    ),
                    'hyperlink' => array(
                        'enable' => false,
                    ),
                ),
                'title' => array(
                    'style' => array(
                        'padding'       => '0px 0px 0px 0px',
                        'margin'        => '15px 0px 10px 0px',
                        'fontSize'      => '25px',
                        'color'         => '#000',
                        'textAlign'     => 'left',
                        'fontWeight'    => '600',
                        'textTransform' => 'none',
                    ),
                ),
                'content' => array(
                    'display' => true,
                    'content_format' => 'excerpt',
                    'style' => array(
                        'fontSize'      => '14px',
                        'color'         => '#556271',
                        'textAlign'     => 'left',
                        'lineHeight'    => '20px',
                        'margin'        => '5px 0px 5px 0px',
                    ),
                ),
                'readMore' => array(
                    'display' => true,
                    'text' => 'Read More',
                    'containerStyle' => array(
                        'textAlign' => 'left',
                    ),
                    'style' => array(
                        'padding'           => '8px 15px 8px 15px',
                        'fontSize'          => '14px',
                        'fontWeight'        => '400',
                        'color'             => '#fff',
                        'textDecoration'    => 'none',
                        'textTransform'     => 'none',
                        'background'        => '#0073aa',
                        'borderRadius'      => '4px',
                        'border'            => '0px solid #e5e5e5',
                        'margin'            => '5px 0px 25px 0px',
                        'display'           => 'inline-block',
                    ),
                ),
                'meta' => array(
                    'display' => true,
                    'separator' => ' | ',
                    'fields' => array(
                        'author' => array(
                            'display' => true,
                            'text' => __( 'Author: ', 'wemail' ),
                        ),
                        'categories' => array(
                            'display' => true,
                            'text' => __( 'Categories: ', 'wemail' ),
                        ),
                        'tags' => array(
                            'display' => true,
                            'text' => __( 'Tags: ', 'wemail' ),
                        ),
                        'postDate' => array(
                            'display' => true,
                            'text' => __( 'Post Date: ', 'wemail' ),
                        ),
                    ),
                    'style' => array(
                        'margin'    => '10px 0px 15px 0px',
                        'fontSize'  => '14px',
                        'display'   => 'inline-block',
                    ),
                ),
                'divider' => array(
                    'display' => true,
                    'style' => array(
                        'borderBottom'  => '1px dashed #ddd',
                        'width'         => '100%',
                        'marginLeft'    => 'auto',
                        'marginRight'   => 'auto',
                        'marginBottom'  => '25px',
                    ),
                ),
            ),
        );
    }
}
