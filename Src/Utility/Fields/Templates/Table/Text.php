<tr class="<?php echo esc_attr($cssClass); ?>">
    <th>
        <?php if ($title) : ?><label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($title); ?></label><?php endif; ?>
    </th>
    <td>
        <input type="text" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($value); ?>" />
        <?php if (!empty($help)) : ?>
            <span class="description"><?php echo esc_html($help); ?></span>
        <?php endif; ?>
    </td>
</tr>