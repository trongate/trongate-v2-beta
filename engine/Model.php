<?php

/**
 * Model Base Class
 *
 * Provides automatic loading and method delegation to module-specific model files.
 * Also serves as the data access layer, providing database connections for both
 * the default database and alternative database groups.
 */
class Model {

    // Cache for loaded model instances
    private array $loaded_models = [];

    // Cache for DB instances (default and alternative database groups)
    private array $db_instances = [];

    // The module that instantiated this Model instance
    private ?string $current_module = null;

    /**
     * Constructor for Model class.
     *
     * @param string|null $module_name The name of the module calling this model.
     */
    public function __construct(?string $module_name = null) {
        $this->current_module = $module_name;
    }

    /**
     * Magic method to provide access to database connections from within model files.
     *
     * This method handles:
     * 1. Primary database access via $this->db (always available)
     * 2. Alternative database groups via $this->groupname (e.g., $this->analytics)
     *
     * Alternative database groups can ONLY be accessed from model files, not controllers.
     * This enforces proper architectural separation.
     *
     * @param string $key The property name (e.g., 'db', 'analytics', 'legacy').
     * @return DB The DB instance for the requested database group.
     * @throws Exception If the property is not a valid database connection.
     */
    public function __get(string $key): DB {
        // Check if already instantiated (cache hit)
        if (isset($this->db_instances[$key])) {
            return $this->db_instances[$key];
        }

        // Handle primary database (always accessible)
        if ($key === 'db') {
            return $this->db_instances[$key] = new DB($this->current_module);
        }

        // Handle alternative database groups
        // Check if this key corresponds to a configured database group
        if ($this->is_database_group($key)) {
            return $this->db_instances[$key] = new DB($this->current_module, $key);
        }

        // Not a valid database connection
        throw new Exception("Undefined property: Model::$key. If '{$key}' is meant to be a database group, ensure it is configured in /config/database.php");
    }

    /**
     * Magic method to handle calls to module-specific model methods.
     * Automatically loads the appropriate <Module>_model.php file and forwards the method call.
     *
     * @param string $method The name of the method being called.
     * @param array $arguments The arguments passed to the method.
     * @return mixed The result of the method call.
     * @throws Exception If the model file or method cannot be found.
     */
    public function __call(string $method, array $arguments) {
        // Get the calling module from the current_module property
        if (!isset($this->current_module)) {
            throw new Exception("Model class cannot determine the calling module. Please ensure the module name is set.");
        }

        $module_name = $this->current_module;

        // Load the model if not already loaded
        if (!isset($this->loaded_models[$module_name])) {
            $this->load_model($module_name);
        }

        // Get the model instance
        $model_instance = $this->loaded_models[$module_name];

        // Check if the method exists in the model
        if (!method_exists($model_instance, $method)) {
            $model_class = ucfirst($module_name) . '_model';
            throw new Exception("Method '{$method}' not found in {$model_class} class.");
        }

        // Call the method and return the result
        return call_user_func_array([$model_instance, $method], $arguments);
    }

    /**
     * Check if a property key corresponds to a configured database group.
     * This checks the global $databases array defined in /config/database.php
     *
     * @param string $key The property name to check.
     * @return bool True if it's a valid database group, false otherwise.
     */
    private function is_database_group(string $key): bool {
        return isset($GLOBALS['databases'][$key]);
    }

    /**
     * Loads a module-specific model file.
     *
     * @param string $module_name The name of the module whose model should be loaded.
     * @return void
     * @throws Exception If the model file or class cannot be found.
     */
    private function load_model(string $module_name): void {
        // Build the model class name and file path
        $model_class = ucfirst($module_name) . '_model';
        $model_path = $this->get_model_path($module_name, $model_class);

        // Require the model file
        require_once $model_path;

        // Check if the class exists
        if (!class_exists($model_class)) {
            throw new Exception("Model class '{$model_class}' not found in {$model_path}");
        }

        // Instantiate the model and cache it
        $this->loaded_models[$module_name] = new $model_class($module_name);
    }

    /**
     * Get the path to a module's model file, handling both standard and child modules.
     *
     * @param string $module_name The name of the module.
     * @param string $model_class The name of the model class.
     * @return string The path to the model file.
     * @throws Exception If the model file cannot be found.
     */
    private function get_model_path(string $module_name, string $model_class): string {
        $possible_paths = [];

        // Priority 1: Standard module path
        $possible_paths[] = '../modules/' . $module_name . '/' . $model_class . '.php';

        // Priority 2: Child module path (for parent-child module structure)
        if (strpos($module_name, '-') !== false) {
            $bits = explode('-', $module_name);

            if (count($bits) === 2 && strlen($bits[1]) > 0) {
                $parent_module = strtolower($bits[0]);
                $child_module = strtolower($bits[1]);
                $model_class_name = ucfirst($child_module) . '_model';
                $possible_paths[] = '../modules/' . $parent_module . '/' . $child_module . '/' . $model_class_name . '.php';
            }
        }

        // Check each possible path
        foreach ($possible_paths as $model_path) {
            if (file_exists($model_path)) {
                return $model_path;
            }
        }

        // Model file not found
        $attempted_paths = implode("\n- ", $possible_paths);
        throw new Exception("Model file '{$model_class}.php' not found for module '{$module_name}'. Attempted paths:\n- {$attempted_paths}");
    }

}
