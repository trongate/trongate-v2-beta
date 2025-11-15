<?php
$admin_theme = [
    "dir" => "default_admin/blue",
    "template" => "admin.php",
];

$themes['admin'] = $admin_theme;

$bootstrappy_theme = [
    "dir" => "bootstrappy/dark",
    "template" => "bootstrappy.php",
];

$themes['bootstrappy'] = $bootstrappy_theme;
define('THEMES', $themes);
