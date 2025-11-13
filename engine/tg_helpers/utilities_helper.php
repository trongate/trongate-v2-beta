<?php
/**
 * Simple memoization function for caching values across function calls.
 *
 * This function provides a persistent cache that survives multiple function calls.
 * It supports three modes of operation:
 * 
 * 1. Retrieve entire cache (no arguments)
 * 2. Cache callable results using object hash as key
 * 3. Get/set values by string key
 *
 * @param string|callable|null $key The cache key (string), a callable to cache, or null to return entire cache.
 * @param mixed $value The value to cache. If a Closure, it will be executed and its result cached.
 * @param mixed $default The default value to return if the key doesn't exist in cache.
 * 
 * @return mixed Returns:
 *               - The entire cache array if $key is null
 *               - The cached result of the callable (memoized by object hash)
 *               - The cached value for the given key, or $default if not found
 *               - The newly set value if $value is provided
 *
 * @example
 * // Store a value
 * memo('user_id', 123);
 * 
 * // Retrieve a value
 * $userId = memo('user_id'); // Returns 123
 * 
 * // Use default if not found
 * $count = memo('count', default: 0); // Returns 0 if 'count' not set
 * 
 * // Cache expensive callable results
 * $result = memo(fn() => expensiveOperation());
 * 
 * // Store using Closure
 * memo('config', fn() => loadConfig());
 * 
 * // Get entire cache
 * $allCached = memo();
 */
function memo(string|callable|null $key = null, mixed $value = null, mixed $default = null): mixed {
    static $cache = [];
    
    if ($key === null) return $cache;
    
    if (is_callable($key)) {
        $hash = spl_object_hash($key);
        return $cache[$hash] ??= $key();
    }
    
    if ($value !== null) {
        return $cache[$key] = ($value instanceof Closure) ? $value() : $value;
    }
    
    return $cache[$key] ?? $default;
}

/**
 * Outputs the given data as JSON in a prettified format, suitable for debugging and visualization.
 * This function is especially useful during development for inspecting data structures in a readable JSON format directly in the browser. 
 * It optionally allows terminating the script immediately after output, useful in API development for stopping further processing.
 *
 * @param mixed $data The data (e.g., array or object) to encode into JSON format. The data can be any type that is encodable into JSON.
 * @param bool|null $kill_script Optionally, whether to terminate the script after outputting the JSON. 
 *                                If true, the script execution is halted immediately after the JSON output.
 *                                Default is null, which means the script continues running unless specified otherwise.
 * @return void Does not return any value; the output is directly written to the output buffer.
 */
function json($data, ?bool $kill_script = null): void {
    echo '<pre>' . json_encode($data, JSON_PRETTY_PRINT) . '</pre>';

    if (isset($kill_script)) {
        die();
    }
}

/**
 * Get the client's IP address.
 *
 * @return string The client's IP address.
 */
function ip_address(): string {
    return $_SERVER['REMOTE_ADDR'];
}

/**
 * Extract file name and extension from a given file path.
 *
 * @param string $file_string The file path from which to extract information.
 * @return array An associative array containing the 'file_name' and 'file_extension'.
 */
function return_file_info(string $file_string): array {
    // Get the file extension
    $file_extension = pathinfo($file_string, PATHINFO_EXTENSION);
    // Get the file name without the extension
    $file_name = str_replace("." . $file_extension, "", $file_string);
    // Return an array containing the file name and file extension
    return array("file_name" => $file_name, "file_extension" => "." . $file_extension);
}

/**
 * Loads a template file with optional data for use within the template.
 *
 * @param string $template_file The filename of the template to load.
 * @param array|null $data (Optional) The data to be passed to the template as an associative array. Defaults to null.
 * 
 * @return void
 */
function load(string $template_file, ?array $data = null): void {
    // Attempt load template view file
    if (isset(THEMES[$template_file])) {
        $theme_dir = THEMES[$template_file]['dir'];
        $template = THEMES[$template_file]['template'];
        $file_path = APPPATH . 'public/themes/' . $theme_dir . '/' . $template;
        define('THEME_DIR', BASE_URL . 'themes/' . $theme_dir . '/');
    } else {
        $file_path = APPPATH . 'templates/views/' . $template_file . '.php';
    }

    if (file_exists($file_path)) {
        // Extract data if provided
        if (isset($data)) {
            extract($data);
        }

        require_once($file_path);
    } else {
        die('<br><b>ERROR:</b> View file does not exist at: ' . $file_path);
    }
}

/**
 * Sorts an array of associative arrays by a specified property.
 *
 * @param array $array The array to be sorted.
 * @param string $property The property by which to sort the array.
 * @param string $direction The direction to sort ('asc' for ascending, 'desc' for descending). Default is 'asc'.
 * @return array The sorted array.
 */
function sort_by_property(array &$array, string $property, string $direction = 'asc'): array {
    usort($array, function($a, $b) use ($property, $direction) {
        // Determine the comparison method based on the property type
        if (is_string($a[$property])) {
            $result = strcasecmp($a[$property], $b[$property]);
        } else {
            $result = $a[$property] <=> $b[$property];
        }
        
        return ($direction === 'desc') ? -$result : $result;
    });
    return $array;
}

/**
 * Sorts an array of objects by a specified property.
 *
 * @param array $array The array of objects to be sorted.
 * @param string $property The property by which to sort the objects.
 * @param string $direction (Optional) The direction of sorting ('asc' or 'desc'). Defaults to 'asc'.
 * @return array The sorted array of objects.
 */
function sort_rows_by_property(array $array, string $property, string $direction = 'asc'): array {
    usort($array, function($a, $b) use ($property, $direction) {
        // Determine the comparison method based on the property type
        if (is_string($a->$property)) {
            $result = strcasecmp($a->$property, $b->$property);
        } else {
            $result = $a->$property <=> $b->$property;
        }
        
        return ($direction === 'desc') ? -$result : $result;
    });
    return $array;
}

/**
 * Checks if the HTTP request has been invoked by Trongate MX.
 *
 * @return bool True if the request has the X-Trongate-MX header set to 'true', otherwise false.
 */
function from_trongate_mx(): bool {
    if (isset($_SERVER['HTTP_TRONGATE_MX_REQUEST'])) {
        return true;
    } else {
        return false;
    }
}
