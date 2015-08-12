<div class="<?php echo esc_attr($cssClass); ?>">
    <?php foreach ($fieldsets as $fieldset) : ?>
    <fieldset>
        <?php foreach ($fieldset as $field) : ?>
            <?php echo $field; ?>
        <?php endforeach; ?>
    </fieldset>
    <?php endforeach; ?>
    <a class="rmLink" href="#"><?php echo esc_html($rmLabel); ?></a>
    <a class="addLink"  href="#"><?php echo esc_html($addLabel); ?></a>
</div>