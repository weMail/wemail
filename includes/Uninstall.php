<?php

namespace WeDevs\WeMail;

class Uninstall
{
    public static function uninstall()
    {
        $data = [
            'deactivated' => true
        ];

        $response = wemail()->api->site()->update_activation_status()->post($data);

        if (is_wp_error($response)) {

            return rest_ensure_response(['data' => 'failed to update status']);
        }

        delete_option('wemail_plugin_active');

        return rest_ensure_response($response);
    }

}
