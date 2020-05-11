<?php

namespace WeDevs\WeMail;

class Uninstall {

    public static function uninstall() {
        $data = [
            'deactivated' => true
        ];

        wemail()->api->site()->update_activation_status()->post($data);
    }

}
