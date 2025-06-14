<?php
session_start();

// Config files
require_once '../config/config.php';
require_once '../config/custom_routing.php';
require_once '../config/database.php';
require_once '../config/site_owner.php';
require_once '../config/themes.php';

// Streamlined autoloader
spl_autoload_register(function ($class_name) {
    $file = __DIR__ . '/' . $class_name . '.php';
    if (file_exists($file)) {
        require_once $file;
        return true;
    }
    return false;
});

/**
 * Retrieves the URL segments after processing custom routes.
 *
 * @return array Returns an associative array with 'assumed_url' and 'segments'.
 */
function get_segments(): array {
    // Figure out how many segments need to be ditched
    $pseudo_url = str_replace('://', '', BASE_URL);
    $pseudo_url = rtrim($pseudo_url, '/');
    $bits = explode('/', $pseudo_url);
    $num_bits = count($bits);

    if ($num_bits > 1) {
        $num_segments_to_ditch = $num_bits - 1;
    } else {
        $num_segments_to_ditch = 0;
    }

    $assumed_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    $assumed_url = attempt_add_custom_routes($assumed_url);

    $data['assumed_url'] = $assumed_url;

    $assumed_url = str_replace('://', '', $assumed_url);
    $assumed_url = rtrim($assumed_url, '/');

    $segments = explode('/', $assumed_url);

    // Remove base segments efficiently
    $data['segments'] = array_slice($segments, $num_segments_to_ditch);
    return $data;
}

/**
 * Optimized route matching using cached regex patterns.
 *
 * @param string $target_url The original target URL to potentially replace.
 * @return string Returns the updated URL if a custom route match is found, otherwise returns the original URL.
 */
function attempt_add_custom_routes(string $target_url): string {
    static $compiled_routes = null;
    
    // Compile routes once and cache
    if ($compiled_routes === null) {
        $compiled_routes = array();
        foreach (CUSTOM_ROUTES as $pattern => $destination) {
            $regex = '/^' . str_replace(array('/', '(:num)', '(:any)'), array('\/', '(\d+)', '([^\/]+)'), $pattern) . '$/';
            $compiled_routes[] = array('regex' => $regex, 'destination' => $destination);
        }
    }
    
    $target_url = rtrim($target_url, '/');
    $target_segments_str = str_replace(BASE_URL, '', $target_url);
    
    foreach ($compiled_routes as $route) {
        if (preg_match($route['regex'], $target_segments_str, $matches)) {
            $new_url = $route['destination'];
            // Replace $1, $2, etc. with captured parameters
            for ($i = 1; $i < count($matches); $i++) {
                $new_url = str_replace('$' . $i, $matches[$i], $new_url);
            }
            return rtrim(BASE_URL . $new_url, '/');
        }
    }
    
    return $target_url;
}

// Define core constants
define('APPPATH', str_replace("\\", "/", dirname(dirname(__FILE__)) . '/'));
define('REQUEST_TYPE', $_SERVER['REQUEST_METHOD']);

// Process URL and routing
$data = get_segments();
define('SEGMENTS', $data['segments']);
define('ASSUMED_URL', $data['assumed_url']);

// Helper files  
require_once 'tg_helpers/flashdata_helper.php';
require_once 'tg_helpers/form_helper.php';
require_once 'tg_helpers/string_helper.php';
require_once 'tg_helpers/timedate_helper.php';
require_once 'tg_helpers/url_helper.php';
require_once 'tg_helpers/utilities_helper.php';