<div class="<?php echo esc_attr($cssClass); ?>">
    <?php if ($preview) : ?>
        <div class="image-preview">
            <img src="<?php echo esc_url($image); ?>">
        </div>
    <?php endif; ?>
    <?php if ($title) : ?>
        <label for="<?php echo esc_attr($id); ?>">
            <?php echo esc_html($title); ?>
        </label>
    <?php endif; ?>
    <input type="text" id="<?php echo esc_attr($id); ?>" name="<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($value); ?>" class="widefat hidden" />
    <input class="<?php echo esc_attr($buttonCssClass); ?> button" type="button" value="Upload Image" />
    <input class="<?php echo esc_attr($buttonCssClass); ?>-remove button" type="button" value="Clear" />
    <?php if ($help) : ?>
        <p class="field-help"><?php echo esc_html($help); ?></p>
    <?php endif; ?>
</div>