<div class="<?php echo esc_attr($cssClass); ?>">
	<p>
		<?php if ($title) : ?><label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($title); ?></label><?php endif; ?>
		<input class="widefat" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>" type="text" value="<?php echo esc_attr($value); ?>" />
	</p>
	<div class="<?php echo esc_attr($id); ?>"></div>
</div>

