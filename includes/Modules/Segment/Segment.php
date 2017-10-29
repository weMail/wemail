<?php

namespace WeDevs\WeMail\Modules\Segment;

class Segment {

    public function conditions() {
        return apply_filters( 'wemail_segment_conditions', [
            'IS'        => __( 'is', 'wemail' ),
            'ISNOT'     => __( 'is not', 'wemail' ),
            'CONT'      => __( 'contains', 'wemail' ),
            'NCONT'     => __( 'not contains', 'wemail' ),
            'BEGINSW'   => __( 'begins with', 'wemail' ),
            'ENDSW'     => __( 'ends with', 'wemail' ),
            'HAS'       => __( 'has', 'wemail' ),
            'HASNOT'    => __( 'has not', 'wemail' ),
            'FROM'      => __( 'from', 'wemail' ),
            'NFROM'     => __( 'not from', 'wemail' ),
            'INGRP'     => __( 'in group', 'wemail' ),
            'NINGRP'    => __( 'not in group', 'wemail' ),
            'UNSUBFRM'  => __( 'unsubscribed from', 'wemail' ),
            'EQUAL'     => __( 'is equal to', 'wemail' ),
            'GT'        => __( 'greater than', 'wemail' ),
            'LT'        => __( 'less than', 'wemail' ),
            'BTWN'      => __( 'between', 'wemail' ),
            'AFTER'     => __( 'After', 'wemail' ),
            'BEFORE'    => __( 'Before', 'wemail' ),
            'IN'        => __( 'in', 'wemail' ),
            'NIN'       => __( 'not in', 'wemail' ),
        ] );
    }

    public function all() {
        return wemail()->api->segments()->all()->get();
    }

    public function items() {
        return wemail()->api->segments()->items()->get();
    }

    public function get( $id ) {
        return wemail()->api->segments( $id )->get();
    }

}
