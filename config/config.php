<?php
//The main config file
define('BASE_URL', 'http://localhost/trongate_live6/');
define('ENV', 'dev');
define('DEFAULT_MODULE', 'welcome');
define('DEFAULT_METHOD', 'index');
define('MODULE_ASSETS_TRIGGER', '_module');

$interceptors = [
    'endpoint_listener' => 'record'
];
define('INTERCEPTORS', $interceptors);