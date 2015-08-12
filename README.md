# kabar

Basic set of libraries I use to speed up WordPress development contained in plugin.

At some point I will provide documentation, but right now it is still work in progress.

# Starting modules

<?php \kabar\ServiceLocator::get('Module', 'Styles'); ?>

# Extending modules/components

Extensions directory should mimic the structure found in Src directory.

<?php
\kabar\ServiceLocator::register(
    'vendornamespace',
    $absolutePathToExtensionsDirectory
);
?>