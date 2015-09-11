<div class="<?php echo esc_attr($cssClass); ?>">
    <label for="<?php echo esc_attr($fieldId); ?>"><?php echo $label; ?></label>
	<?php if (!empty($help)) : ?>
		<p class="field-help"><?php echo esc_html($help); ?></p>
	<?php endif; ?>
    <select multiple name="<?php echo esc_attr($fieldName); ?>[]" id="<?php echo esc_attr($fieldId); ?>">
        <?php foreach ($options as $optLabel => $optValue) : ?>
            <option value="<?php echo esc_attr($optValue); ?>" <?php echo !empty($value) && in_array($optValue, $value) ? 'selected="selected"' : ''; ?>><?php echo $optLabel; ?></option>
        <?php endforeach; ?>
    </select>
</div>