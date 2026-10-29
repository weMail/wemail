<?php
defined( 'ABSPATH' ) || exit;
?>
<p>
    <label>
        <?php esc_html_e( 'Title', 'wemail' ); ?>:
        <input type="text" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" value="<?php echo esc_attr( $title ); ?>">
    </label>
</p>

<p>
    <label>
        <?php esc_html_e( 'Select a Form', 'wemail' ); ?>:
        <select class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'form' ) ); ?>">
            <option value=""><?php esc_html_e( 'Select a form', 'wemail' ); ?></option>
            <?php foreach ( $forms as $form ) : ?>
                <option
                    value="<?php echo esc_attr( $form['id'] ); ?>"
                    <?php echo $selected === $form['id'] ? 'selected' : ''; ?>
                ><?php echo esc_html( $form['name'] ); ?></option>
            <?php endforeach; ?>
        </select>
        <p class="description"><i><?php esc_html_e( 'Only modal and inline types are shown here.', 'wemail' ); ?></i></p>
    </label>
</p>
