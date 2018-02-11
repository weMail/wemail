<div class="wemail">
    <script type="text/javascript">
        if (!window.wemail_forms) {
            window.wemail_forms = [];
        }

        window.wemail_forms['<?php echo $id; ?>'] = <?php echo json_encode( $form ); ?>
    </script>

    <div class="wemail-form-container">
        <wemail-form id="<?php echo $id; ?>"></wemail-form>
    </div>
</div>
