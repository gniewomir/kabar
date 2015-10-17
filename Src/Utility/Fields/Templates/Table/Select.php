<tr class="<?php echo esc_attr($cssClass); ?>">
    <th>
        <?php if ($title) : ?><label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($title); ?></label><?php endif; ?>
    </th>
    <td>
        <select name="<?php echo esc_attr($id); ?>" id="<?php echo esc_attr($id); ?>">
            <?php if (is_null($default)) { echo '<option selected="selected"></option>'; } ?>
            <?php foreach ($options as $optLabel => $optValue) : ?>
                <option value="<?php echo esc_attr($optValue); ?>" <?php echo $value == $optValue ? 'selected="selected"' : ''; ?>><?php echo esc_html($optLabel); ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (!empty($help)) : ?>
            <p class="field-help"><?php echo esc_html($help); ?></p>
        <?php endif; ?>
    </td>
</tr>