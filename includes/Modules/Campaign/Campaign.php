<?php

namespace WeDevs\WeMail\Modules\Campaign;

use WeDevs\WeMail\Framework\Module;
use WeDevs\WeMail\Modules\Campaign\Event;
use WeDevs\WeMail\Modules\Campaign\Editor;

class Campaign extends Module {

    private $menu_priority = 2;

    public $event;
    public $editor;

    public function __construct() {
        $this->add_filter( 'wemail_admin_submenu', 'register_submenu', $this->menu_priority, 2 );
        $this->add_filter( 'wemail_get_route_data_campaignIndex', 'index', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_campaignCreate', 'create', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_campaignShow', 'show', 10, 2 );
        $this->add_filter( 'wemail_get_route_data_campaignEdit', 'edit', 10, 2 );

        $this->event  = new Event();
        $this->editor = new Editor();
    }

    public function register_submenu( $menu_items, $capability ) {
        $menu_items[] = [ __( 'Campaigns', 'wemail' ), $capability, 'admin.php?page=wemail#/campaigns' ];

        return $menu_items;
    }

    public function index( $params, $query ) {
        return [
            'i18n' => [
                'campaigns' => __( 'Campaigns', 'wemail' )
            ],
            'campaigns' => wemail()->api->campaigns()->query( $query )->get()
        ];
    }

    public function create( $params, $query ) {
        return $this->editor->get_setup_data();
    }

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
        $campaign['email']['template'] = [
            'globalCss' => [
                'backgroundColor' => '#fafafa',
                'borderTopColor' => '#0073aa',
                'borderTopStyle' => 'solid',
                'borderTopWidth' => '0px',
                'paddingBottom' => '0px',
                'paddingTop' => '0px',
            ],
            'globalElementStyles' => [
                'a' => [
                    'color' => 'yellow',
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
                        \WeDevs\WeMail\Modules\Customizer\ContentTypes::text(),
                        \WeDevs\WeMail\Modules\Customizer\ContentTypes::video(),
                    ]
                ]
            ]
        ];

        return $campaign;
    }

    public function edit( $params, $query ) {
        $data = $this->editor->get_setup_data();

        $campaign = $this->testTemplate( $this->get( $params['id'] ) );

        $data['campaign']     = $campaign;
        $data['customizer']   = $this->editor->get_customizer_data();

        return $data;
    }

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
