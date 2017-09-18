<?php

namespace WeDevs\WeMail\Modules\Campaign;

class Editor {

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

}
