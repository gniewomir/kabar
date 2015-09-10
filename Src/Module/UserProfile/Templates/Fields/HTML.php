<tr class="<?php echo esc_attr($cssClass); ?>">
    <th scope="row">
        <label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($title); ?></label>
    </th>
    <td>
        <?php if (!empty($help)) : ?>
            <p class="field-help"><?php echo esc_html($help); ?></p>
        <?php endif; ?>
        <?php if ($wysiwyg) : ?>
            <?php wp_editor( $value, $id); ?>
        <?php else : ?>
            <textarea class="widefat" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>"><?php echo esc_textarea($value); ?></textarea>
        <?php endif; ?>
    </td>
</tr>
