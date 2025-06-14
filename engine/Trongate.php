<?php

/**
 * Trongate Base Controller Class
 * 
 * The foundation class that all application controllers extend.
 * Provides core functionality for templates, views, modules, and file uploads.
 * Rebuilt for maximum performance - no Dynamic_properties trait, explicit class loading.
 */
class Trongate {

    // Framework class instances (lazy-loaded)
    private ?Model $model = null;
    private ?Validation $validation = null;
    private ?File $file = null;
    private ?Image $image = null;
    private ?Template $template = null;
    
    // Core properties
    protected ?string $module_name = '';
    protected string $parent_module = '';
    protected string $child_module = '';

    /**
     * Framework classes accessible via $this->property syntax
     */
    private const CORE_CLASSES = [
        'model' => Model::class,
        'validation' => Validation::class,
        'file' => File::class,
        'image' => Image::class,
        'template' => Template::class,
    ];

    /**
     * Constructor for Trongate class.
     * 
     * @param string|null $module_name The name of the module to use, or null for default module.
     */
    public function __construct(?string $module_name = null) {
        $this->module_name = $module_name;
    }

    /**
     * Magic getter for framework classes - preserves $this->model syntax while avoiding Dynamic_properties overhead.
     *
     * @param string $key The property name.
     * @return object The class instance.
     * @throws Exception If the property is not supported.
     */
    public function __get(string $key): object {
        if (!isset(self::CORE_CLASSES[$key])) {
            throw new Exception("Undefined property: " . get_class($this) . "::$key");
        }
        
        if ($this->$key === null) {
            $class = self::CORE_CLASSES[$key];
            // Model needs module name, others don't
            $this->$key = $key === 'model' 
                ? new $class($this->module_name)
                : new $class();
        }
        
        return $this->$key;
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
        $template_controller_path = '../templates/controllers/Templates.php';
        
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
     * Loads a module using the Modules class.
     *
     * This method serves as an alternative way of invoking the load method from the Modules class.
     * It simply instantiates a Modules object and calls its load method with the provided target module name.
     *
     * @param string $target_module The name of the target module.
     * @return void
     */
    protected function module(string $target_module): void {
        $modules = new Modules;
        $modules->load($target_module);
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