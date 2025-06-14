<?php

/**
 * Core Framework Dispatcher
 * 
 * The main request router that handles three types of requests:
 * 1. Vendor assets (/vendor/library/file.css) - serves third-party library files
 * 2. Module assets (module_module/js/script.js) - serves assets from module directories
 * 3. Controller requests - standard MVC routing to controllers and methods
 * 
 * Supports complex module structures including parent/child modules (e.g., cars-accessories).
 * Instantiated on every request immediately after framework bootstrap.
 */
class Core {

    protected $current_module = DEFAULT_MODULE;
    protected $current_controller = DEFAULT_CONTROLLER;
    protected $current_method = DEFAULT_METHOD;
    protected $current_value = '';

    /**
     * Constructor for the Core class.
     * Depending on the URL, serves either vendor assets, controller content, or module assets.
     */
    public function __construct() {
        if (strpos(ASSUMED_URL, '/vendor/')) {
            $this->serve_vendor_asset();
        } elseif (strpos(ASSUMED_URL, MODULE_ASSETS_TRIGGER) === false) {
            $this->serve_controller();
        } else {
            $this->serve_module_asset();
        }
    }

    /**
     * Serve controller class.
     *
     * @return void
     */
    private function serve_controller(): void {
        $segments = SEGMENTS;

        // Parse module from segments
        if (isset($segments[1])) {
            $module_with_no_params = explode('?', $segments[1])[0];
            $this->current_module = !empty($module_with_no_params) ? strtolower($module_with_no_params) : $this->current_module;
            $this->current_controller = ucfirst($this->current_module);
        }

        // Parse method from segments  
        if (isset($segments[2])) {
            $method_with_no_params = explode('?', $segments[2])[0];
            $this->current_method = !empty($method_with_no_params) ? strtolower($method_with_no_params) : $this->current_method;

            // Block access to private methods (starting with _)
            if (substr($this->current_method, 0, 1) === '_') {
                $this->draw_error_page();
            }
        }

        // Get optional parameter value
        $this->current_value = $segments[3] ?? '';

        // Build controller path and load controller
        $controller_path = $this->get_controller_path();
        require_once $controller_path;

        // Dev environment: check for SQL transfers
        if (strtolower(ENV) === 'dev') {
            $this->attempt_sql_transfer($controller_path);
        }

        $this->invoke_controller_method();
    }

    /**
     * Get the correct controller path, handling child modules and 404 fallbacks.
     *
     * @return string The path to the controller file
     */
    private function get_controller_path(): string {
        $controller_path = '../modules/' . $this->current_module . '/controllers/' . $this->current_controller . '.php';

        if (file_exists($controller_path)) {
            return $controller_path;
        }

        // Try child controller
        $child_path = $this->try_child_controller();
        if ($child_path !== null) {
            return $child_path;
        }

        // Try custom 404 intercept
        $intercept_path = $this->try_404_intercept();
        if ($intercept_path !== null) {
            return $intercept_path;
        }

        // All options exhausted
        $this->draw_error_page();
    }

    /**
     * Attempt to find a child controller.
     *
     * @return string|null The controller path if found, null otherwise
     */
    private function try_child_controller(): ?string {
        $bits = explode('-', $this->current_controller);

        if (count($bits) === 2 && strlen($bits[1]) > 0) {
            $parent_module = strtolower($bits[0]);
            $child_module = strtolower($bits[1]);
            $this->current_controller = ucfirst($bits[1]);
            
            $controller_path = '../modules/' . $parent_module . '/' . $child_module . '/controllers/' . ucfirst($bits[1]) . '.php';
            
            if (file_exists($controller_path)) {
                return $controller_path;
            }
        }

        return null;
    }

    /**
     * Attempt to use custom 404 intercept.
     *
     * @return string|null The controller path if found, null otherwise
     */
    private function try_404_intercept(): ?string {
        if (defined('INTERCEPT_404')) {
            $intercept_bits = explode('/', INTERCEPT_404);
            $this->current_module = $intercept_bits[0];
            $this->current_controller = ucfirst($intercept_bits[0]);
            $this->current_method = $intercept_bits[1];
            
            $controller_path = '../modules/' . $this->current_module . '/controllers/' . $this->current_controller . '.php';
            
            if (file_exists($controller_path)) {
                return $controller_path;
            }
        }

        return null;
    }

    /**
     * Invoke the appropriate controller method.
     *
     * @return void
     */
    private function invoke_controller_method(): void {
        $controller_class = $this->current_controller;
        $controller_instance = new $controller_class($this->current_module);

        if (method_exists($controller_instance, $this->current_method)) {
            $controller_instance->{$this->current_method}($this->current_value);
        } elseif (method_exists($controller_instance, 'index')) {
            $controller_instance->index($this->current_value);
        }
    }

    /**
     * Attempt SQL transfer for Module Import Wizard.
     *
     * @param string $controller_path The path to the controller.
     * @return void
     */
    private function attempt_sql_transfer(string $controller_path): void {
        $ditch = 'controllers/' . $this->current_controller . '.php';
        $dir_path = str_replace($ditch, '', $controller_path);

        $files = array();
        foreach (glob($dir_path . "*.sql") as $file) {
            $file = str_replace($controller_path, '', $file);
            $files[] = $file;
        }

        if (count($files) > 0) {
            require_once('tg_transferer/index.php');
            die();
        }
    }

}