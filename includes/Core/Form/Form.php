<?php

namespace WeDevs\WeMail\Core\Form;

use WeDevs\WeMail\Traits\Singleton;

class Form {

    use Singleton;

    /**
     * Get paginated list of forms
     *
     * @since 1.0.0
     *
     * @param array $query
     *
     * @return array
     */
    public function all( $query ) {
        return wemail()->api->forms()->query( $query )->get();
    }

    /**
     * Get a single form data
     *
     * @since 1.0.0
     *
     * @param string $id
     *
     * @return array|null
     */
    public function get( $id ) {
        global $wpdb;

        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $query = $wpdb->prepare( "SELECT * FROM {$this->get_table()} WHERE `id` = %s AND deleted_at IS NULL AND `status` = 1", $id );

        $form = $wpdb->get_row( $query, ARRAY_A );

        if ( $form ) {
            $form['settings'] = json_decode( $form['settings'], true );
            $form['template'] = json_decode( $form['template'], true );

            return $form;
        }

        return null;
    }

    /**
     * Get all form items
     *
     * Id-name paired items
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function items() {
        $forms = wemail()->api->forms()->items()->get();

        if ( is_wp_error( $forms ) ) {
            return [];
        } elseif ( ! empty( $forms['data'] ) ) {
            return $forms['data'];
        }

        return null;
    }

    /**
     * Post a form submission
     *
     * @since 1.0.0
     *
     * @param string $id
     * @param array $data
     *
     * @return null|array|WP_Error
     */
    public function submit( $id, $data ) {
        $form = wemail()->api->forms( $id )->submit()->post( $data );

        return $form;
    }

    /**
     * Name of supported form integrations
     *
     * @since 1.0.0
     *
     * @return array
     */
    public function integrations() {
        return [
            'contact_form_7'    => __( 'Contact Form 7', 'wemail' ),
            'gravity_forms'     => __( 'Gravity Forms', 'wemail' ),
            'wpforms'           => __( 'WPForms', 'wemail' ),
            'caldera_forms'     => __( 'Caldera Forms', 'wemail' ),
            'weforms'           => __( 'weForms', 'wemail' ),
            'ninja_forms'       => __( 'Ninja Forms', 'wemail' ),
            'fluent_forms'      => __( 'Fluent Forms', 'wemail' ),
            'happy_forms'       => __( 'Happy Forms', 'wemail' ),
            'formidable_forms'  => __( 'Formidable Forms', 'wemail' ),
            'popup_builder'     => __( 'Popup Builder', 'wemail' ),
            'popup_maker'       => __( 'Popup Maker', 'wemail' ),
            'affiliate_wp'       => __( 'AffiliateWP', 'wemail' ),
        ];
    }

    public function increment_visitor_count( $form_id ) {
        $form = wemail()->api->forms( $form_id )->visitors()->put( [] );

        return $form;
    }

    /**
     * Insert a new form
     *
     * @param $data
     *
     * @return bool|false|int
     */
    public function create( $data ) {
        global $wpdb;

        $data = $this->fillable_check( $data, [ 'id', 'name', 'template', 'settings', 'type' ] );

        $data = array_merge( $data, [ 'plugin_version' => wemail()->version ] );

        $this->to_json_string( $data );

        return $wpdb->insert( $this->get_table(), $data );
    }

    /**
     * Update form data
     *
     * @param $id
     * @param $data
     *
     * @return bool|false|int
     */
    public function update( $data, $id ) {
        global $wpdb;

        $ids = (array) $id;

        $data = $this->fillable_check(
            $data,
            [ 'name', 'template', 'settings', 'type', 'status', 'deleted_at' ]
        );

        $data = array_merge( $data, [ 'plugin_version' => wemail()->version ] );

        $this->to_json_string( $data );

        $attrs = array_map(
            function ( $key ) use ( $data ) {
                return ( '`' . $key . '` = ' . ( is_null( $data[ $key ] ) ? 'NULL' : '%s' ) );
            },
            array_keys( $data )
        );

        $attrs = implode( ', ', $attrs );

        $data = array_filter(
            $data,
            function ( $attr ) {
                return ! is_null( $attr );
            }
        );

        $ids_sql = $this->in_sql( $ids );
        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $statement = $wpdb->prepare( "UPDATE {$this->get_table()} SET {$attrs} WHERE `id` IN({$ids_sql})", $data );

        return $wpdb->query( $statement );
    }

    /**
     * Delete forms
     *
     * @param $ids
     * @param bool $soft_delete
     *
     * @return bool|false|int
     */
    public function delete( $ids, $soft_delete = false ) {
        global $wpdb;
        $ids = (array) $ids;

        if ( $soft_delete ) {
            return $this->update( [ 'deleted_at' => current_time( 'mysql' ) ], $ids );
        }

        $ids_string = $this->in_sql( $ids );

        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        return $wpdb->query( "DELETE FROM {$this->get_table()} WHERE `id` IN ({$ids_string})" );
    }

    /**
     * Get all active forms
     *
     * @param array $args
     *
     * @return array|array[]
     */
    public function get_forms( $args = [] ) {
        global $wpdb;

        $args = array_merge(
            [
                'type' => [ 'floating-bar', 'floating-box', 'modal' ],
                'select' => [ '*' ],
            ],
            $args
        );

        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $forms = $wpdb->get_results( "SELECT {$this->in_sql( $args['select'], false )} FROM {$this->get_table()} WHERE `type` IN ({$this->in_sql( $args['type'] )}) AND `status` = 1 AND `deleted_at` IS NULL", ARRAY_A );

        if ( is_null( $forms ) ) {
            return [];
        }

        foreach ( $forms as $index => $form ) {
            if ( isset( $form['settings'] ) ) {
                $forms[ $index ]['settings'] = json_decode( $form['settings'], true );
            }

            if ( isset( $form['template'] ) ) {
                $forms[ $index ]['template'] = json_decode( $form['template'], true );
            }
        }

        return $forms;
    }

    public function sync() {
        global $wpdb;

        $forms = $this->all(
            [
                'per_page'     => -1,
                'with_trashed' => 1,
                'fields'       => 'id,name,template,settings,plugin_version,type,status,deleted_at',
            ]
        );

        // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
        $local_forms = $wpdb->get_results( "SELECT * FROM {$this->get_table()}" );

        if ( is_wp_error( $forms ) || is_null( $local_forms ) ) {
            return null;
        }

        $api_forms = $forms['data'];
        $api_form_ids = array_column( $api_forms, 'id' );
        $local_form_ids = array_column( $local_forms, 'id' );
        // Handle common forms
        $common_ids = array_intersect( $api_form_ids, $local_form_ids );

        $common_forms = array_filter(
            $api_forms,
            function ( $form ) use ( $common_ids ) {
                return in_array( $form['id'], $common_ids, true );
            }
        );

        foreach ( $common_forms as $common_form ) {
            $this->update( $common_form, $common_form['id'] );
        }

        // Creating New Forms
        $new_ids = array_diff( $api_form_ids, $local_form_ids );
        if ( ! empty( $new_ids ) ) {
            $new_forms = array_filter(
                $api_forms,
                function ( $form ) use ( $new_ids ) {
                    return in_array( $form['id'], $new_ids, true );
                }
            );

            foreach ( $new_forms as $new_form ) {
                $this->create( $new_form );
            }
        }

        // Delete forms
        $deleted_ids = array_diff( $local_form_ids, $api_form_ids );

        if ( ! empty( $deleted_ids ) ) {
            $this->delete( $deleted_ids );
        }

        return $forms;
    }

    /**
     * Convert array to json string
     *
     * @param $data
     * @param string[] $columns
     */
    protected function to_json_string( &$data, $columns = [ 'template', 'settings' ] ) {
        foreach ( $columns as $column ) {
            if ( isset( $data[ $column ] ) ) {
                $data[ $column ] = wp_json_encode( $data[ $column ] );
            }
        }
    }

    /**
     * Get the form table name
     *
     * @return string
     */
    protected function get_table() {
        global $wpdb;

        return $wpdb->prefix . 'wemail_forms';
    }

    /**
     * Check form columns
     *
     * @param $data
     * @param array $columns
     *
     * @return array
     */
    protected function fillable_check( $data, $columns = [] ) {
        return array_filter(
            $data,
            function ( $value, $key ) use ( $columns ) {
                return in_array( $key, $columns, true );
            },
            ARRAY_FILTER_USE_BOTH
        );
    }

    /**
     * Get In ids
     *
     * @param $items
     *
     * @param bool $quote
     *
     * @return string
     */
    protected function in_sql( $items, $quote = true ) {
        return implode(
            ', ',
            array_map(
                function ( $item ) use ( $quote ) {
                    if ( $quote ) {
                        return "'" . esc_sql( $item ) . "'";
                    }
                    return esc_sql( $item );
                },
                $items
            )
        );
    }
}
