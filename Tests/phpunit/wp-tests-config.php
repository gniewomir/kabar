<?php

// change the next line to points to your wordpress dir
define( 'ABSPATH', dirname(dirname(__DIR__)).'/wordpress/' );

define( 'WP_DEBUG', false );

// WARNING WARNING WARNING!
// tests DROPS ALL TABLES in the database. DO NOT use a production database

/** The name of the database for WordPress */
define('DB_NAME', 'kabar_tests');
/** MySQL database username */
define('DB_USER', 'kabar');
/** MySQL database password */
define('DB_PASSWORD', 'kabar');
/** MySQL hostname */
define('DB_HOST', 'localhost');
/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');
/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

define('WP_DEFAULT_THEME', 'twentyfifteen');

$table_prefix = 'wptests_'; // Only numbers, letters, and underscores please!

define( 'WP_TESTS_DOMAIN', 'uwazaj.dev' );
define( 'WP_TESTS_EMAIL', 'gniewomir.swiechowski@gmail.com' );
define( 'WP_TESTS_TITLE', 'Kabar library test' );

define( 'WP_PHP_BINARY', 'php' );

define( 'WPLANG', '' );