<?php
defined( 'ABSPATH' ) || exit;
?>
<div class="wemail">
    <script type="text/javascript">
        if (!window.wemail_forms) {
            window.wemail_forms = [];
        }

        window.wemail_forms['<?php echo esc_js( $id ); ?>'] = <?php echo wp_json_encode( $form ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON for JS context ?>
    </script>

    <div class="wemail-form-container">
        <wemail-form id="<?php echo esc_attr( $id ); ?>"></wemail-form>
    </div>
</div>
