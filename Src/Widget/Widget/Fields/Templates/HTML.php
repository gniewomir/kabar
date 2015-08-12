<div class="<?php echo esc_attr($cssClass); ?>">
	<label for="<?php echo esc_attr($fieldId); ?>"><?php echo esc_html($label); ?> <em>(html & shortcodes allowed)</em></label>
	<p class="help-text"><?php echo esc_html($help); ?></p>
	<textarea class="widefat" rows="<?php echo esc_attr($rows); ?>" cols="20" id="<?php echo esc_attr($fieldId); ?>" name="<?php echo esc_attr($fieldName); ?>"><?php echo $value; ?></textarea>
</div>