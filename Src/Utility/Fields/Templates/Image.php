<div class="<?php echo esc_attr($cssClass); ?>">
	<?php if ($title) : ?><label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($title); ?></label><?php endif; ?>
	<input type="text" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($value); ?>" class="widefat" size="25" />
	<input class="<?php echo esc_attr($buttonCssClass); ?> button" type="button" value="Upload Image" />
	<?php if ($help) : ?>
		<p class="field-help"><?php echo esc_html($help); ?></p>
	<?php endif; ?>
</div>