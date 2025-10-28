<?php
// Database connection credentials
// The 'default' group is used when you call $this->db
// Additional groups can be accessed via $this->groupname in model files (e.g., $this->analytics)
// Note: Alternative database groups can ONLY be accessed from model files, not controllers
$databases = [
    'default' => [
        'host' => '127.0.0.1',
        'port' => '3306',
        'user' => 'root',
        'password' => '',
        'database' => 't2'
    ],
    'df_web' => [
        'host' => '127.0.0.1',
        'port' => '3306',
        'user' => 'root',
        'password' => '',
        'database' => 'df_web'
    ]
];

// Example: Multiple database configuration
// Uncomment and modify the section below to add additional database connections
/*
$databases = [
    'default' => [
        'host' => '127.0.0.1',
        'port' => '3306',
        'user' => 'root',
        'password' => '',
        'database' => 'default_db'
    ],
    'analytics' => [
        'host' => '127.0.0.1',
        'port' => '3306',
        'user' => 'analytics_user',
        'password' => 'analytics_pass',
        'database' => 'analytics_db'
    ],
    'legacy' => [
        'host' => '192.168.1.50',
        'port' => '3306',
        'user' => 'legacy_user',
        'password' => 'legacy_pass',
        'database' => 'old_system'
    ]
];
*/
