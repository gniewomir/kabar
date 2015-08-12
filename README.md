# kabar

Basic set of libraries I use to speed up WordPress development contained in a plugin.

At some point I will provide documentation and usage examples, but right now it is still work in progress.

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

# Changelog

2.16.0 - Moved project to GitHub and wrapped in plugin