<tr class="<?php echo esc_attr($cssClass); ?>">
    <th>
        <label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($title); ?></label>
    </th>
    <td>
        <?php if ($preview) : ?>
            <div class="image-preview">
                <img src="<?php echo esc_url($value); ?>">
            </div>
        <?php endif; ?>
        <input type="text" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($value); ?>" class="widefat"/>
        <input class="<?php echo esc_attr($buttonCssClass); ?> button" type="button" value="Upload Image" />
        <input class="<?php echo esc_attr($buttonCssClass); ?>-remove button" type="button" value="Clear" />
        <?php if (!empty($help)) : ?>
            <span class="description"><?php echo esc_html($help); ?></span>
        <?php endif; ?>
    </td>
</tr>