<?php

namespace WeDevs\WeMail\Modules\Campaign;

use WeDevs\WeMail\Framework\Module;
use WeDevs\WeMail\Modules\Campaign\Event;
use WeDevs\WeMail\Modules\Campaign\Editor;

class Campaign extends Module {

    /**
     * Submenu priority
     *
     * @since 1.0.0
     *
     * @var integer
     */
    private $menu_priority = 2;

    /**
     * Event class container
     *
     * @since 1.0.0
     *
     * @var WeDevs\WeMail\Modules\Campaign\Event
     */
    public $event;

    /**
     * Event class container
     *
     * @since 1.0.0
     *
     * @var WeDevs\WeMail\Modules\Campaign\Editor
     */
    public $editor;

    /**
     * Class constructor
     *
     * @since 1.0.0
     *
     * @return void
     */
    public function __construct() {
        $this->add_filter( 'wemail_admin_submenu', 'register_submenu', $this->menu_priority, 2 );
        $this->add_filter( 'wemail_get_route_data_campaignIndex', 'index', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_campaignCreate', 'create', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_campaignShow', 'show', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_campaignEdit', 'edit', 10, 2 );

        $this->event  = new Event();
        $this->editor = new Editor();
    }

    /**
     * Register submenu
     *
     * @since 1.0.0
     *
     * @param array $menu_items
     * @param string $capability
     *
     * @return array
     */
    public function register_submenu( $menu_items, $capability ) {
        $menu_items[] = [ __( 'Campaigns', 'wemail' ), $capability, 'admin.php?page=wemail#/campaigns' ];

        return $menu_items;
    }

    /**
     * Campaign list table data
     *
     * @since 1.0.0
     *
     * @param array $params
     * @param array $query
     *
     * @return array
     */
    public function index( $params, $query ) {
        return [
            'i18n' => [
                'campaigns' => __( 'Campaigns', 'wemail' )
            ],
            'campaigns' => wemail()->api->campaigns()->query( $query )->get()
        ];
    }

    /**
     * Campaign create route data
     *
     * @since 1.0.0
     *
     * @param array $params
     * @param array $query
     *
     * @return array
     */
    public function create( $params, $query ) {
        return $this->editor->get_setup_data();
    }

    /**
     * Campaign single page route data
     *
     * @since 1.0.0
     *
     * @param array $params
     * @param array $query
     *
     * @return array
     */
    public function show( $params, $query ) {
        return [
            'i18n' => [
                'campaign' => __( 'List', 'wemail' )
            ],
            'campaign' => $this->get( $params['id'] )
        ];
    }

    // this will remove after finishing the templating system
    public function testTemplate( $campaign ) {
        // text
        $text_content = \WeDevs\WeMail\Modules\Customizer\ContentTypes::text();
        $text_content = array_merge( [ 'id' => 1, 'type' => 'text' ], $text_content['default'] );

        // image
        $image_content = \WeDevs\WeMail\Modules\Customizer\ContentTypes::image();
        $image_content = array_merge( [ 'id' => 2, 'type' => 'image' ], $image_content['default'] );

        // image caption
        $image_caption = \WeDevs\WeMail\Modules\Customizer\ContentTypes::image_caption();
        $image_caption = array_merge( [ 'id' => 3, 'type' => 'imageCaption' ], $image_caption['default'] );

        // social follow
        $social_follow = \WeDevs\WeMail\Modules\Customizer\ContentTypes::social_follow();
        $social_follow['default']['icons'] = [
            [
                'site' => 'facebook',
                'link' => 'http://facebook.com/wedevs',
                'text' => 'Facebook',
            ],
            [
                'site' => 'twitter',
                'link' => 'http://twitter.com/wedevs',
                'text' => 'Twitter',
            ],
            [
                'site' => 'website',
                'link' => 'http://wedevs.com',
                'text' => 'Website'
            ]
        ];
        $social_follow = array_merge( [ 'id' => 4, 'type' => 'socialFollow' ], $social_follow['default'] );

        // button
        $button = \WeDevs\WeMail\Modules\Customizer\ContentTypes::button();
        $button = array_merge( [ 'id' => 5, 'type' => 'button' ], $button['default'] );

        // divider
        $divider = \WeDevs\WeMail\Modules\Customizer\ContentTypes::divider();
        $divider = array_merge( [ 'id' => 6, 'type' => 'divider' ], $divider['default'] );

        $template = [
            'globalCss' => [
                'backgroundColor' => '#fafafa',
                'borderTopColor' => '#0073aa',
                'borderTopStyle' => 'solid',
                'borderTopWidth' => '0px',
                'paddingTop' => '0px',
                'paddingBottom' => '0px',
                'fontFamily' => 'arial',
                'fontSize' => '14px',
                'color' => '#444',
            ],
            'globalElementStyles' => [
                'a' => [
                    'color' => '#0073aa',
                    'textDecoration' => 'none'
                ],
            ],
            'sections' => [
                [
                    'name' => 'top',
                    'style' => [
                        'backgroundColor' => ''
                    ],
                    'wrapperStyle' => [
                        'backgroundColor' => '#fff',
                        'borderBottom' => '0px solid #dddddd',
                        'borderLeft' => '0px solid #dddddd',
                        'borderRight' => '0px solid #dddddd',
                        'borderTop' => '0px solid #dddddd',
                        'marginBottom' => '0px',
                        'paddingBottom' => '0px',
                        'paddingLeft' => '0px',
                        'paddingRight' => '0px',
                        'paddingTop' => '0px',
                        'maxWidth' => '600px'
                    ],
                    'contents' => [
                        $text_content,
                        $social_follow,
                    ]
                ],
                [
                    'name' => 'head',
                    'style' => [
                        'backgroundColor' => ''
                    ],
                    'wrapperStyle' => [
                        'backgroundColor' => '#fff',
                        'borderBottom' => '0px solid #dddddd',
                        'borderLeft' => '0px solid #dddddd',
                        'borderRight' => '0px solid #dddddd',
                        'borderTop' => '0px solid #dddddd',
                        'marginBottom' => '0px',
                        'paddingBottom' => '0px',
                        'paddingLeft' => '0px',
                        'paddingRight' => '0px',
                        'paddingTop' => '0px',
                        'maxWidth' => '600px'
                    ],
                    'contents' => [
                        $divider,
                        $button,
                        $image_content,
                        $image_caption,
                        \WeDevs\WeMail\Modules\Customizer\ContentTypes::footer(),
                    ]
                ],
                [
                    'name' => 'body',
                    'style' => [
                        'backgroundColor' => ''
                    ],
                    'wrapperStyle' => [
                        'backgroundColor' => '#fff',
                        'borderBottom' => '0px solid #dddddd',
                        'borderLeft' => '0px solid #dddddd',
                        'borderRight' => '0px solid #dddddd',
                        'borderTop' => '0px solid #dddddd',
                        'marginBottom' => '0px',
                        'paddingBottom' => '0px',
                        'paddingLeft' => '0px',
                        'paddingRight' => '0px',
                        'paddingTop' => '0px',
                        'maxWidth' => '600px'
                    ],
                    'contents' => [
                        $image_caption,
                    ]
                ],
                [
                    'name' => 'footer',
                    'style' => [
                        'backgroundColor' => ''
                    ],
                    'wrapperStyle' => [
                        'backgroundColor' => '#fff',
                        'borderBottom' => '0px solid #dddddd',
                        'borderLeft' => '0px solid #dddddd',
                        'borderRight' => '0px solid #dddddd',
                        'borderTop' => '0px solid #dddddd',
                        'marginBottom' => '0px',
                        'paddingBottom' => '0px',
                        'paddingLeft' => '0px',
                        'paddingRight' => '0px',
                        'paddingTop' => '0px',
                        'maxWidth' => '600px'
                    ],
                    'contents' => [

                        \WeDevs\WeMail\Modules\Customizer\ContentTypes::video(),
                    ]
                ]
            ]
        ];

        $id = 1;
        foreach ( $template['sections'] as &$section ) {
            foreach ( $section['contents'] as &$content ) {
                $content['id'] = $id;
                ++$id;
            }
        }

        $campaign['email']['template'] = $template;

        return $campaign;
    }

    /**
     * Campaign editing page route data
     *
     * @since 1.0.0
     *
     * @param array $params
     * @param array $query
     *
     * @return array
     */
    public function edit( $params, $query ) {
        $data = $this->editor->get_setup_data();

        $campaign = $this->testTemplate( $this->get( $params['id'] ) );

        $data['campaign']     = $campaign;
        $data['customizer']   = $this->editor->get_customizer_data();

        return $data;
    }

    /**
     * Get campaign data
     *
     * @since 1.0.0
     *
     * @param integer $id
     *
     * @return array
     */
    public function get( $id ) {
        $campaign = wemail()->api->campaigns( $id )->get();

        if ( isset( $campaign['data'] ) ) {
            $campaign = $campaign['data'];

            if ( empty( $campaign['email']['template'] ) ) {
                $campaign['email']['template'] = function () {};
            }

        } else {
            $campaign = null;
        }

        return $campaign;
    }

}
