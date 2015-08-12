<div class="<?php echo esc_attr($cssClass); ?>">
	<label for="<?php echo esc_attr($fieldId); ?>"><?php echo esc_html($label); ?></label>
	<input id="<?php echo esc_attr($fieldId); ?>" name="<?php echo esc_attr($fieldName); ?>"  class="widefat" type="text" value="<?php echo esc_url($value, array('http', 'https')); ?>" />
	<input class="kabar-upload-image-button button" type="button" value="Upload Image" />
</div>