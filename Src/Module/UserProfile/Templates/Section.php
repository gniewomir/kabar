<div class="<?php echo esc_attr($containerCssClass); ?>">
    <h3 id="<?php echo esc_attr($formId); ?>-title"><?php echo esc_html($title); ?></h3>
    <table class="form-table" id="<?php echo esc_attr($formId); ?>-table">
        <?php echo $formNonce; ?>
        <?php foreach ($formFields as $field) : ?>
            <?php echo $field; ?>
        <?php endforeach; ?>
    </table>
</div>