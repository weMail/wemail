<?php

namespace WeDevs\WeMail\Core\Help\Services;

class PingService {

    protected $test_input_key = 'wemail_test_input';
    protected $test_input_value = 'wemail_test_value';

    /**
     * Send a test request to weMail api server
     * @param $request
     * @param $callback_url
     * @return void
     */
    public function request_send( $request, $callback_url ) {
        $api_key = get_user_meta( get_current_user_id(), 'wemail_api_key', true );

        if ( ! $api_key ) {
            return wp_send_json_error(
                [
                    'message' => 'User ID not found',
                ]
            );
        }

        $api = apply_filters(
            'wemail_api_url',
            'https://api.getwemail.io/v1'
        );

        $wemail_api = untrailingslashit( $api );

        $response = wp_remote_post(
            $wemail_api . '/help/tools/ping',
            [
                'method' => 'POST',
                'headers'     => [
                    'x-api-key' => $api_key,
                ],
                'body'        => [
                    'wemail_test_input' => $request['wemail_test_input'],
                    'callback_url'      => $callback_url,
                ],
            ]
        );

        if ( is_wp_error( $response ) ) {
            $error_message = $response->get_error_message();
            return wp_send_json_error(
                [
                    'message' => 'Something went wrong: ' . $error_message,
                ]
            );
        } else {
            $body = wp_remote_retrieve_body( $response );
            $code = wp_remote_retrieve_response_code( $response );
            $result = array_merge(
                [
                    'message' => 'Server responded with ' . $code . ' code.',
                ],
                json_decode( $body, true )
            );

            return wp_send_json_success(
                $result,
                $code
            );
        }
    }

    /**
     * Send a test request to weMail api server
     * @param $request
     * @return array
     */
    public function request_receive( $request ) {
        return [
            'wemail_test_input' => $request['wemail_test_input'] ? $request['wemail_test_input'] : null,
            'received_headers'  => $request['received_headers'] ? $request['received_headers'] : null,
        ];
    }
}
