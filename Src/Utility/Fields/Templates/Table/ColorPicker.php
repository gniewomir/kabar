<tr class="<?php echo esc_attr($cssClass); ?>">
    <th scope="row">
        <?php echo esc_html($title); ?>
    </th>
    <td>
        <label for="<?php echo esc_attr($id); ?>">
        <input type="text" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($value); ?>" size="25" />
            <?php echo esc_html($help); ?>
        </label>
    </td>
</tr>