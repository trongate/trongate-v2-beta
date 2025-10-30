<?php

/**
 * Trongate Base Controller Class
 * 
 * The foundation class that all application controllers extend.
 * Provides core functionality for templates, views, modules, and file uploads.
 */
class Trongate {

    // Instance cache for lazy loading
    private array $instances = [];
    
    // Loaded modules cache
    private array $loaded_modules = [];
    
    // Core properties
    protected ?string $module_name = '';
    protected string $parent_module = '';
    protected string $child_module = '';

    /**
     * Constructor for Trongate class.
     * 
     * @param string|null $module_name The name of the module to use, or null for default module.
     */
    public function __construct(?string $module_name = null) {
        $this->module_name = $module_name;
    }

    /**
     * Magic getter for framework classes and loaded modules.
     *
     * @param string $key The property name.
     * @return object The class instance.
     * @throws Exception If the property is not supported.
     */
    public function __get(string $key): object {
        // Check if it's a loaded module first
        if (isset($this->loaded_modules[$key])) {
            return $this->loaded_modules[$key];
        }

        // Handle core framework classes with lazy loading
        $core_instance = match($key) {
            'db' => new DB($this->module_name),
            'model' => new Model($this->module_name),
            'validation' => new Validation(),
            'file' => new File(),
            'image' => new Image(),
            'template' => new Template(),
            default => null
        };

        if ($core_instance !== null) {
            return $this->instances[$key] ??= $core_instance;
        }

        // If not a core class, try to load it as a module (automatic module loading)
        try {
            $this->module($key);
            return $this->loaded_modules[$key];
        } catch (Exception $e) {
            throw new Exception("Undefined property: " . get_class($this) . "::$key");
        }
    }

    /**
     * Renders a specific template view by calling a corresponding method in the Templates controller class.
     *
     * @param string $template_name The name of the template method to be called.
     * @param array $data An associative array containing data to be passed to the template method.
     * @return void
     * @throws Exception If template controller or method is not found.
     */
    protected function template(string $template_name, array $data = []): void {
        $template_controller_path = '../templates/Templates.php';
        
        if (!file_exists($template_controller_path)) {
            $template_controller_path = str_replace('../', APPPATH, $template_controller_path);
            throw new Exception('ERROR: Unable to find Templates controller at ' . $template_controller_path . '.');
        }
        
        require_once $template_controller_path;
        $templates = new Templates;

        if (!method_exists($templates, $template_name)) {
            $template_controller_path = str_replace('../', APPPATH, $template_controller_path);
            throw new Exception('ERROR: Unable to find ' . $template_name . ' method in ' . $template_controller_path . '.');
        }

        if (!isset($data['view_file'])) {
            $data['view_file'] = DEFAULT_METHOD;
        }

        $templates->$template_name($data);
    }

    /**
     * Loads a module and makes it available as a property.
     *
     * @param string $target_module The name of the target module.
     * @return void
     */
    protected function module(string $target_module): void {
        // Don't reload if already loaded
        if (isset($this->loaded_modules[$target_module])) {
            return;
        }

        // Build the controller path and class name
        $controller_class = ucfirst($target_module);
        $controller_path = '../modules/' . $target_module . '/' . $controller_class . '.php';
        $is_child_module = false;

        // If standard path doesn't exist, try child module
        if (!file_exists($controller_path)) {
            $child_module_info = $this->try_child_module_path($target_module);
            $controller_path = $child_module_info['path'];
            $controller_class = $child_module_info['class']; // Use the correct child module class name
            $is_child_module = true;
        }
        
        // Load and instantiate the module
        require_once $controller_path;
        
        if (!class_exists($controller_class)) {
            throw new Exception("Module class not found: {$controller_class}");
        }
        
        // Create the module instance
        $module_instance = new $controller_class($target_module);
        
        // Store the module instance using the original target_module as key
        $this->loaded_modules[$target_module] = $module_instance;
        
        // For child modules, also store under the child module name for easy access
        if ($is_child_module) {
            $bits = explode('-', $target_module);
            if (count($bits) === 2) {
                $child_module_name = strtolower($bits[1]);
                $this->loaded_modules[$child_module_name] = $module_instance;
            }
        }
    }

    /**
     * Try to find a child module controller.
     *
     * @param string $target_module The target module name.
     * @return array An array containing 'path' and 'class' keys.
     * @throws Exception If the controller cannot be found.
     */
    private function try_child_module_path(string $target_module): array {
        $bits = explode('-', $target_module);

        if (count($bits) === 2 && strlen($bits[1]) > 0) {
            $parent_module = strtolower($bits[0]);
            $child_module = strtolower($bits[1]);
            $controller_class = ucfirst($child_module); // This is the correct class name

            $controller_path = '../modules/' . $parent_module . '/' . $child_module . '/' . $controller_class . '.php';

            if (file_exists($controller_path)) {
                return [
                    'path' => $controller_path,
                    'class' => $controller_class
                ];
            }
        }

        throw new Exception("Module controller not found: {$target_module}");
    }

    /**
     * Upload a picture file using the upload method from the Image class.
     *
     * This method serves as an alternative way of invoking the upload method from the Image class.
     * It simply uses the lazy-loaded Image instance and calls its upload method with the provided configuration data.
     *
     * @param array $config The configuration data for handling the upload.
     * @return array|null The information of the uploaded file.
     */
    protected function upload_picture(array $config): ?array {
        return $this->image->upload($config);
    }

    /**
     * Upload a file using the upload method from the File class.
     *
     * This method serves as an alternative way of invoking the upload method from the File class.
     * It simply uses the lazy-loaded File instance and calls its upload method with the provided configuration data.
     *
     * @param array $config The configuration data for handling the upload.
     * @return array|null The information of the uploaded file.
     */
    protected function upload_file(array $config): ?array {
        return $this->file->upload($config);
    }

    /**
     * Renders a view file with optional data.
     *
     * This method can either display the view on the browser or return the generated contents as a string.
     *
     * @param string $view The name of the view file to render.
     * @param array $data Optional. An associative array of data to pass to the view. Default is an empty array.
     * @param bool|null $return_as_str Optional. Whether to return the rendered view as a string. Default is null.
     *                                If set to true, the view content will be returned as a string; if set to false or null,
     *                                the view will be displayed on the browser. Default is null, which means the view will be displayed.
     * @return string|null If $return_as_str is true, the rendered view as a string; otherwise, null.
     * @throws Exception If the view file is not found.
     */
    protected function view(string $view, array $data = [], ?bool $return_as_str = null): ?string {
        $return_as_str = $return_as_str ?? false;

        if (isset($data['view_module'])) {
            $module_name = $data['view_module'];
        } else {
            $module_name = strtolower(get_class($this));
        }

        $view_path = $this->get_view_path($view, $module_name);
        extract($data);

        if ($return_as_str) {
            // Output as string
            ob_start();
            require $view_path;
            return ob_get_clean();
        } else {
            // Output view file
            require $view_path;
            return null;
        }
    }

    /**
     * Get the path of a view file with optimized fallback logic.
     *
     * @param string $view The name of the view file.
     * @param string|null $module_name Module name to which the view belongs.
     *
     * @return string The path of the view file.
     * @throws Exception If the view file does not exist.
     */
    private function get_view_path(string $view, ?string $module_name): string {
        $possible_paths = [];

        // Priority 1: Child module path (if parent/child modules are set)
        if ($this->parent_module !== '' && $this->child_module !== '') {
            $possible_paths[] = APPPATH . "modules/{$this->parent_module}/{$this->child_module}/views/{$view}.php";
        }

        // Priority 2: Standard module path
        $possible_paths[] = APPPATH . "modules/{$module_name}/views/{$view}.php";

        // Priority 3: Derive module name from URL segment (for parent-child modules)
        $segment_one = segment(1);
        if (strpos($segment_one, '-') !== false && substr_count($segment_one, '-') === 1) {
            $module_name_from_segment = str_replace('-', '/', $segment_one);
            $possible_paths[] = APPPATH . "modules/{$module_name_from_segment}/views/{$view}.php";
        }

        // Check each path in order of priority
        foreach ($possible_paths as $view_path) {
            if (file_exists($view_path)) {
                return $view_path;
            }
        }

        // No view found - throw exception with helpful error message
        $attempted_paths = implode("\n- ", $possible_paths);
        throw new Exception("View '{$view}' not found. Attempted paths:\n- {$attempted_paths}");
    }
}