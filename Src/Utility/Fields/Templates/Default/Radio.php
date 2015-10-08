<fieldset class="<?php echo esc_attr($cssClass); ?>">
    <legend><p><?php echo esc_html($title); ?></p></legend>
    <?php foreach ($options as $label => $value) : ?>
    <label>
        <input type="radio" name="<?php echo esc_attr($id); ?>" value="<?php echo esc_attr($value); ?>" <?php if ($value == $this->value) { echo 'checked="checked"'; } ?> >
        <span><?php echo esc_html($label); ?></span>
    </label><br>
    <?php endforeach; ?>
</fieldset>
