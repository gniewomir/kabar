<div class="<?php echo esc_attr($cssClass); ?>">
	<?php if ($title) : ?><label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($title); ?></label><?php endif; ?>
	<?php if (!empty($help)) : ?>
		<p class="field-help"><?php echo esc_html($help); ?></p>
	<?php endif; ?>
	<textarea class="widefat" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>"><?php echo esc_textarea($value); ?></textarea>
</div>