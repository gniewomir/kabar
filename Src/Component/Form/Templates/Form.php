<form id="<?php echo esc_attr($id); ?>" action="<?php echo esc_attr($action); ?>" method="<?php echo esc_attr($method); ?>" name="<?php echo esc_attr($name); ?>" class="<?php echo esc_attr($cssClass); ?>">
	<?php echo $nonce; ?>
	<?php foreach ($fields as $field) : ?>
		<?php echo $field; ?>
	<?php endforeach; ?>
</form>