<?php
//The main config file
define('BASE_URL', 'http://localhost/trongate_v2_beta/');
define('ENV', 'dev');
define('DEFAULT_MODULE', 'welcome');
define('DEFAULT_METHOD', 'index');
define('MODULE_ASSETS_TRIGGER', '_module');
<<<<<<< HEAD
define('ERROR_404', 'templates/error_404');
define('FLASHDATA_OPEN', '<p style="color: green;">');
define('FLASHDATA_CLOSE', '</p>');

$interceptors = [
    'endpoint_listener' => 'record'
];
define('INTERCEPTORS', $interceptors);
=======
define('ERROR_404', 'templates/error_404');
>>>>>>> 00f2c1a7004d893d6670f0411588cbdc41cb0802
