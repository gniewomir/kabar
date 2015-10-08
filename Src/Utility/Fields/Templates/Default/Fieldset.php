<fieldset id="<?php echo esc_attr($id); ?>" class="<?php echo esc_attr($cssClass); ?>">
	<?php if (!empty($title)) : ?>
		<legend><?php echo esc_html($title); ?></legend>
	<?php endif; ?>
	<?php foreach ($fields as $field) : ?>
		<?php echo $field; ?>
	<?php endforeach; ?>
</fieldset>