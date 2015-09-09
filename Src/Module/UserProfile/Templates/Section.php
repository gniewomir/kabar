<div class="<?php echo esc_attr($containerCssClass); ?>">
    <h3 id="<?php echo esc_attr($id); ?>-title"><?php echo esc_html($title); ?></h3>
    <table class="form-table" id="<?php echo esc_attr($id); ?>-table">
        <?php echo $nonce; ?>
        <?php foreach ($fields as $field) : ?>
            <?php echo $field; ?>
        <?php endforeach; ?>
    </table>
</div>