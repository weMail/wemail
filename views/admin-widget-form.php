<p>
    <label>
        <?php _e( 'Title', 'wemail' ); ?>:
        <input type="text" class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>">
    </label>
</p>

<p>
    <label>
        <?php _e( 'Select a Form', 'wemail' ); ?>:
        <select class="widefat" name="<?php echo $this->get_field_name( 'form' ); ?>">
            <option value=""><?php _e('Select a form');?></option>
            <?php foreach ( $forms as $form ): ?>
                <option
                    value="<?php echo $form['id']; ?>"
                    <?php echo $selected === $form['id'] ? 'selected' : ''; ?>
                ><?php echo $form['name']; ?></option>
            <?php endforeach; ?>
        </select>
        <p class="description"><i><?php _e('Only modal and inline types are shown here.', 'wemail');?></i></p>
    </label>
</p>
