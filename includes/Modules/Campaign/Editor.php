<?php

namespace WeDevs\WeMail\Modules\Campaign;

use WeDevs\WeMail\Framework\Traits\Hooker;

class Editor {

    use Hooker;

    public static $image_dir;

    public function __construct() {
        self::$image_dir = WEMAIL_ASSETS . '/images/content-types';

        $this->add_filter( 'wemail_customizer_content_type_settings_campaign', 'content_type_settings' );
        $this->add_filter( 'wemail_customizer_i18n_campaign', 'i18n' );
    }

    public function get_setup_data() {
        return [
            'i18n' => [
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
            ],
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

    public function get_customizer_data() {
        $content_types = [
            'text',
            'image',
            'imageGroup',
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

    public function content_type_settings( $settings ) {
        $additional_types = [
            'wpPosts'          => self::wp_posts(),
            'wpLatestContents' => self::wp_latest_contents()
        ];

        $settings = array_merge( $settings, $additional_types );

        return $settings;
    }

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

    public function i18n( $i18n ) {
        return array_merge( $i18n, [
            'wpPosts'           => __( 'WP Posts', 'wemail' ),
            'wpLatestContents'  => __( 'Latest Contents', 'wemail' ),
        ] );
    }

}
