<p class="<?php echo esc_attr($cssClass); ?>">
	<label for="<?php echo esc_attr($id); ?>" class="selectit">
		<input name="<?php echo esc_attr($id); ?>" type="checkbox" id="<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($value); ?>" <?php echo esc_attr($checked); ?> >
		<?php echo esc_html($title); ?>
	</label>
    <?php if (!empty($help)) : ?>
        <p class="field-help"><?php echo esc_html($help); ?></p>
    <?php endif; ?>
</p>