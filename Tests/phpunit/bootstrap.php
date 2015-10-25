<?php

// path to test lib bootstrap.php
$test_lib_bootstrap_file = dirname(__FILE__) . '/includes/bootstrap.php';

if (!file_exists($test_lib_bootstrap_file)) {
    echo PHP_EOL."Error : unable to find ".$test_lib_bootstrap_file.PHP_EOL;
    exit(''.PHP_EOL);
}

// set plugin and options for activation
$GLOBALS[ 'wp_tests_options' ] = array(
        'active_plugins' => array(
            'kabar/kabar.php'
        ),
        'current_theme' => 'twentyfifteen',
        'kabar_test' => true
);


// call test-lib's bootstrap.php
require_once $test_lib_bootstrap_file;

$current_user = new WP_User(1);
$current_user->set_role('administrator');

echo PHP_EOL;
echo 'Using WordPress core : ' . ABSPATH . PHP_EOL;
echo PHP_EOL;

define('KABAR_ROOT_DIR', dirname(dirname(__DIR__)).'/');
define('KABAR_SRC_DIR', dirname(dirname(__DIR__)).'/Src/');
define('KABAR_FIXTURES_DIR', __DIR__.'/fixtures/');
define('KABAR_UNIT_TESTING', true);

require_once dirname(dirname(__DIR__)).'/Src/Bootstrap.php';

\Kabar::library()->register('fixtures', __DIR__.'/fixtures');
