<?php

namespace WeDevs\WeMail\Rest;

use WeDevs\WeMail\RestController;

class Remote extends RestController {

    public $rest_base = '/remote';

    /**
     * The weMail API paths whose payload is persisted by the SaaS and later used
     * as the site credential for server-to-WP callbacks.
     *
     * Exact matches only — third-party import endpoints (e.g. /import/mailchimp/*)
     * carry the user's own external api_key and must never be overwritten.
     *
     * @since 2.1.7
     *
     * @var array
     */
    private $site_key_paths = array(
        '/import/csv/start',
        '/import/mail-poet/start',
    );

    /**
     * T1 — denied for everyone (session, admin and service). Zero SPA callers.
     *
     * Each entry is a normalized, lowercase path with no leading or trailing
     * slash, matched on segment boundaries by path_is().
     *
     * @since 2.1.7
     *
     * @var array
     */
    private $denied_paths = array(
        'site-api-token',
        'access-token',          // segment match: matches access-token and access-token/*, NEVER access-tokens
        'integration-site',
        'billing/subscriptions',
    );

    public function register_routes() {
        $this->post( '/', 'call_remote', 'remote_permission' );
    }

    /**
     * Permission callback for the /remote proxy
     *
     * Composes the session/service gate with the proxied path gate.
     *
     * @since 2.1.7
     *
     * @param \WP_REST_Request $request
     *
     * @return bool|\WP_Error
     */
    public function remote_permission( $request ) {
        if ( ! $this->can_remote_call( $request ) ) {   // magic: session role OR service key (M1)
            return false;
        }

        return $this->remote_path_allowed( $request );
    }

    /**
     * Recursively sanitize input data (array/object/string)
     */
    private function wemail_recursive_sanitize( $data ) {
        if ( is_array( $data ) ) {
            foreach ( $data as $key => $value ) {
                $data[ $key ] = $this->wemail_recursive_sanitize( $value );
            }
            return $data;
        } elseif ( is_bool( $data ) ) {
            return $data;
        } elseif ( is_email( $data ) ) {
            return sanitize_email( $data );
        } elseif ( is_numeric( $data ) ) {
            return absint( $data );
        } elseif ( filter_var( $data, FILTER_VALIDATE_URL ) ) {
            return esc_url_raw( $data );
        } else {
            return sanitize_text_field( $data );
        }
    }

    /**
     * Stamp the site API key into the payload of allow-listed proxy paths
     *
     * @since 2.1.7
     *
     * @param string $path Proxied weMail API path
     * @param array  $data Request payload
     *
     * @return array
     */
    private function stamp_site_api_key( $path, $data ) {
        $parts    = explode( '?', $path );
        $endpoint = untrailingslashit( $parts[0] );

        if ( in_array( $endpoint, $this->site_key_paths, true ) ) {
            $data['api_key'] = wemail()->api->get_api_key();
        }

        return $data;
    }

    /**
     * Normalize a proxied path for gate matching; false = reject (malformed)
     *
     * @since 2.1.7
     *
     * @param string $url Proxied weMail API URL
     *
     * @return string|false
     */
    private function normalize_remote_path( $url ) {
        $path  = str_replace( wemail()->wemail_api, '', (string) $url );
        $parts = preg_split( '/[?#]/', $path, 2 );
        $path  = rawurldecode( $parts[0] );

        // Residual encoding, dot-segments or backslashes can make the server route
        // differently from what we matched — refuse instead of guessing.
        if ( false !== strpos( $path, '%' ) || false !== strpos( $path, '..' ) || false !== strpos( $path, '\\' ) ) {
            return false;
        }

        $path = preg_replace( '#/+#', '/', $path );
        $path = trim( strtolower( $path ), '/' );

        // A lone "." segment survives the ".." check but still reroutes the
        // request server-side — refuse it like any other malformed path.
        foreach ( explode( '/', $path ) as $segment ) {
            if ( '.' === $segment ) {
                return false;
            }
        }

        return $path;
    }

    /**
     * Segment-boundary prefix match: "access-token" matches "access-token" and
     * "access-token/x" but not "access-tokens".
     *
     * @since 2.1.7
     *
     * @param string $path   Normalized path
     * @param string $prefix  Normalized path prefix
     *
     * @return bool
     */
    private function path_is( $path, $prefix ) {
        return $path === $prefix || 0 === strpos( $path, $prefix . '/' );
    }

    /**
     * Whether the current request may proxy the requested path
     *
     * Matching order is load-bearing: the T1 hard deny runs before any admin
     * shortcut, then T2 admin-only paths, then the T3 catch-all.
     *
     * @since 2.1.7
     *
     * @param \WP_REST_Request $request
     *
     * @return bool|\WP_Error
     */
    private function remote_path_allowed( $request ) {
        $method = strtolower( sanitize_text_field( (string) $request->get_param( 'method' ) ) );

        if ( ! in_array( $method, array( 'get', 'post', 'put', 'patch', 'delete' ), true ) ) {
            return $this->forbidden();
        }

        $path = $this->normalize_remote_path( esc_url_raw( $request->get_param( 'url' ) ) );

        if ( false === $path ) {
            return $this->forbidden();
        }

        // 1. T1 hard deny — before any admin shortcut.
        foreach ( $this->denied_paths as $denied ) {
            if ( $this->path_is( $path, $denied ) ) {
                return $this->forbidden();
            }
        }

        // 2. Admin session passes every remaining path.
        if ( current_user_can( 'manage_options' ) ) {
            return true;
        }

        // 3. T2 admin-only (non-admins and pure service calls get 403).
        if ( $this->remote_path_requires_admin( $path, $method ) ) {
            return $this->forbidden();
        }

        // 4. T3 catch-all — already passed the role/service gate.
        return true;
    }

    /**
     * T2 admin-only rules, method-scoped
     *
     * @since 2.1.7
     *
     * @param string $path   Normalized path
     * @param string $method Lowercase HTTP method
     *
     * @return bool
     */
    private function remote_path_requires_admin( $path, $method ) {
        $is_get = 'get' === $method;

        return ( $this->path_is( $path, 'settings' ) && ! $is_get )      // GET /settings/* stays open (email-outbound bootstrap, email-default-settings)
            || $this->path_is( $path, 'settings/wemail-setup-ongoing' )   // any method
            || ( $this->path_is( $path, 'sending-domains' ) && ! $is_get )
            || $this->path_is( $path, 'access-tokens' )                   // any method; plural only
            || ( $this->path_is( $path, 'onboarding' ) && ! $is_get )     // C4: tighter than POST-only
            || $this->path_is( $path, 'stripe' )
            || $this->path_is( $path, 'sync/subscribers/erp' )
            || 'site-users' === $path                                     // exact
            || $this->path_is( $path, 'team-users' )
            || $this->path_is( $path, 'mailbox' );
    }

    /**
     * Forbidden response for gated proxy paths
     *
     * @since 2.1.7
     *
     * @return \WP_Error
     */
    private function forbidden() {
        return $this->respond_error( __( 'You are not allowed to call this endpoint.', 'wemail' ), 'rest_forbidden', self::HTTP_FORBIDDEN );
    }

    /**
     * Call remote api | weMail api
     * This is proxy to call weMail api
     *
     * @since 2.0.0
     * @since 2.1.7 Stamps site api_key for allow-listed import paths.
     *
     * @param \WP_REST_Request $request
     *
     * @return \WP_REST_Response|mixed
     */
    public function call_remote( $request ) {
        $url = esc_url_raw( $request->get_param( 'url' ) );
        $method = strtolower( sanitize_text_field( $request->get_param( 'method' ) ) );

        $data = $request->get_param( 'data' );
        if ( is_array( $data ) ) {
            $data = $this->wemail_recursive_sanitize( $data );
        }

        $query = $request->get_param( 'query' );
        if ( is_array( $query ) ) {
            $query = $this->wemail_recursive_sanitize( $query );
        }

        $wemail_api_base = wemail()->wemail_api;
        $path = str_replace( $wemail_api_base, '', $url );

        if ( $method === 'get' ) {
            $response = wemail()->api->send_json()->get( $path, $query );
        } else {
            $data = array_merge( $data ? $data : array(), $query ? $query : array() );
            $data = $this->stamp_site_api_key( $path, $data );
            $response = wemail()->api->url( $path )->send_json()->{$method}( $data, array() );
        }

        // Defense in depth: drop any accidental echo of the site API key VALUE
        // from proxied responses, whatever key name it rides on.
        if ( is_array( $response ) ) {
            $response = $this->remove_site_key_values( $response );
        }

        return $response;
    }

    /**
     * Recursively remove entries whose value equals the site API key
     *
     * Defense in depth: matches by VALUE, not key name, so the site credential
     * is dropped from a proxied response no matter which key carries it.
     * Comparison is hash_equals( site key, value ) — the known value first.
     *
     * @since 2.1.7
     *
     * @param array $data
     *
     * @return array
     */
    private function remove_site_key_values( $data ) {
        $site_key = get_option( 'wemail_api_key' );

        // No key configured (or an empty one) — nothing to match; never match "".
        if ( ! is_string( $site_key ) || '' === $site_key ) {
            return $data;
        }

        $removed = false;

        foreach ( $data as $key => $value ) {
            if ( is_array( $value ) ) {
                $data[ $key ] = $this->remove_site_key_values( $value );
                continue;
            }

            if ( is_string( $value ) && '' !== $value && hash_equals( $site_key, $value ) ) {
                unset( $data[ $key ] );
                $removed = true;
            }
        }

        // Keep numeric lists lists after a removal (JSON [] not {}).
        $keys = array_keys( $data );

        if ( $removed && array_filter( $keys, 'is_int' ) === $keys ) {
            $data = array_values( $data );
        }

        return $data;
    }
}
