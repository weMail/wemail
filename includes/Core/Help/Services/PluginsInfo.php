<?php

namespace WeDevs\WeMail\Core\Help\Services;

class PluginsInfo {
    /**
     * Get the list of active and inactive plugins
     *
     * @return array
     */
    public function plugins() {
        // Ensure get_plugins function is loaded
        if ( ! function_exists( 'get_plugins' ) ) {
            include ABSPATH . '/wp-admin/includes/plugin.php';
        }

        $plugins             = get_plugins();
        $active_plugins_keys = get_option( 'active_plugins', [] );
        $active_plugins      = [];

        foreach ( $plugins as $k => $v ) {
            // Take care of formatting the data how we want it.
            $formatted         = [];
            $formatted['name'] = strip_tags( $v['Name'] );

            if ( isset( $v['Version'] ) ) {
                $formatted['version'] = strip_tags( $v['Version'] );
            }

            if ( isset( $v['Author'] ) ) {
                $formatted['author'] = strip_tags( $v['Author'] );
            }

            if ( isset( $v['Network'] ) ) {
                $formatted['network'] = strip_tags( $v['Network'] );
            }

            if ( isset( $v['AuthorURI'] ) ) {
                $formatted['author_uri'] = strip_tags( $v['AuthorURI'] );
            }

            if ( isset( $v['PluginURI'] ) ) {
                $formatted['plugin_uri'] = strip_tags( $v['PluginURI'] );
            }

            if ( in_array( $k, $active_plugins_keys, true ) ) {
                // Remove active plugins from list so we can show active and inactive separately
                unset( $plugins[ $k ] );
                $active_plugins[ $k ] = $formatted;
            } else {
                $plugins[ $k ] = $formatted;
            }
        }

        return [
            'active_plugins' => $active_plugins,
            'inactive_plugins' => $plugins,
        ];
    }
}
