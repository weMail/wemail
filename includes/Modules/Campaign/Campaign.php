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
        $text_content = \WeDevs\WeMail\Modules\Customizer\ContentTypes::text();
        $text_content = array_merge( [ 'id' => 1, 'type' => 'text' ], $text_content['default'] );

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
                        \WeDevs\WeMail\Modules\Customizer\ContentTypes::button(),
                        \WeDevs\WeMail\Modules\Customizer\ContentTypes::divider(),
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
                        \WeDevs\WeMail\Modules\Customizer\ContentTypes::image(),
                        \WeDevs\WeMail\Modules\Customizer\ContentTypes::image_caption(),
                        \WeDevs\WeMail\Modules\Customizer\ContentTypes::image_group(),
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
                        \WeDevs\WeMail\Modules\Customizer\ContentTypes::social_follow(),
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
