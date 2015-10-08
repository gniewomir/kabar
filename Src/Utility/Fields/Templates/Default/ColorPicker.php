<p class="<?php echo esc_attr($cssClass); ?>">
	<?php if ($title) : ?><label for="<?php echo esc_attr($id); ?>"><?php echo esc_html($title); ?></label><?php endif; ?>
	<input type="text" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($value); ?>" size="25" />
    <?php if (!empty($help)) : ?>
        <p class="field-help"><?php echo esc_html($help); ?></p>
    <?php endif; ?>
</p>