<?php

namespace WeDevs\WeMail\FrontEnd;

use WP_Widget;

class Widget extends WP_Widget {

    public function __construct() {
        $args = array(
            'classname'   => 'wemail-form-widget',
            'description' => __( 'weMail Form Widget', 'wemail' ),
        );

        parent::__construct( 'wemail-form-widget', __( 'weMail Form', 'wemail' ), $args );
    }

    public function form( $instance ) {
        $forms = wemail()->form->get_forms( array( 'type' => array( 'inline', 'modal' ) ) );

        $defaults = array(
            'title' => __( 'Subscribe to our newsletter', 'wemail' ),
            'form'  => '',
        );

        $instance = wp_parse_args( $instance, $defaults );

        $title = sanitize_text_field( $instance['title'] );
        $selected = $instance['form'];

        include WEMAIL_VIEWS . '/admin-widget-form.php';
    }

    public function widget( $args, $instance ) {
        $title = ! empty( $instance['title'] ) ? $instance['title'] : '';

        echo $args['before_widget'];

        if ( $title ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        if ( empty( $instance['form'] ) ) {
            return __( 'Form Id is missing', 'wemail' );
        }

        $id = $instance['form'];

        $form = wemail()->form->get( $id );

        if ( ! $form || is_wp_error( $form ) ) {
            return __( 'Form not found!', 'wemail' );
        }

        unset( $form['entries'] );
        unset( $form['created_at'] );
        unset( $form['deleted_at'] );
        unset( $form['settings']['actions'] );

        wp_enqueue_script( 'wemail-frontend' );

        include WEMAIL_VIEWS . '/form.php';

        echo $args['after_widget'];
    }
}
