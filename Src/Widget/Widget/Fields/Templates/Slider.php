<div class="<?php echo esc_attr($cssClass); ?>">
	<label for="<?php echo esc_attr($fieldId); ?>"><?php echo esc_html($label); ?></label>
	<input class="widefat" id="<?php echo esc_attr($fieldId); ?>" name="<?php echo esc_attr($fieldName); ?>" type="text" value="<?php echo esc_attr($value); ?>">
	<div class="kabar-slider-container">
		<div class="<?php echo esc_attr($slug); ?>"></div>
	</div>
	<?php echo $script; ?>
</div>

