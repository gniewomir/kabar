<form id="<?php echo esc_attr($formId); ?>" action="<?php echo esc_attr($formAction); ?>" method="<?php echo esc_attr($formMethod); ?>" name="<?php echo esc_attr($formId); ?>" class="<?php echo esc_attr($formCssClass); ?>">
	<?php echo $formNonce; ?>
	<?php foreach ($formFields as $field) : ?>
		<?php echo $field; ?>
	<?php endforeach; ?>
</form>