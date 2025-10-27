<?php

/**
 * Class Modules - Handles serving controller content from a given view file.
 */
class Modules {

    private $modules = [];

    /**
     * Run a module's controller action.
     *
     * @param string $moduleControllerAction The format is "Module/Controller/Action".
     * @param mixed $first_value (Optional) First parameter for the action.
     * @param mixed $second_value (Optional) Second parameter for the action.
     * @param mixed $third_value (Optional) Third parameter for the action.
     * 
     * @return mixed The result of the controller action.
     */
    public static function run(string $moduleControllerAction, $first_value = null, $second_value = null, $third_value = null) {
        $debris = explode('/', $moduleControllerAction);
        $target_module = $debris[0];
        $target_controller = ucfirst($target_module);
        $target_method = $debris[1];
        $controller_path = '../modules/' . $target_module . '/' . $target_controller . '.php';

        if (!file_exists($controller_path)) {
            // Attempt to find child module
            $bits = explode('-', $target_module);

            if (count($bits) === 2 && strlen($bits[1]) > 0) {
                $parent_module = $bits[0];
                $child_module = $bits[1];
                $target_controller = ucfirst($child_module);
                $controller_path = '../modules/' . $parent_module . '/' . $child_module . '/' . $target_controller . '.php';
            }
        }

        require_once $controller_path;
        $controller = new $target_controller($target_module);
        return $controller->$target_method($first_value, $second_value, $third_value);
    }

    /**
     * Loads a module by instantiating its controller.
     *
     * @param string $target_module The name of the target module.
     * @return void
     */
    public function load(string $target_module): void {
        $target_controller = ucfirst($target_module);
        $target_controller_path = '../modules/' . $target_module . '/' . $target_controller . '.php';

        if (!file_exists($target_controller_path)) {
            // Try child module path
            $bits = explode('-', $target_module);

            if (count($bits) === 2 && strlen($bits[1]) > 0) {
                $parent_module = strtolower($bits[0]);
                $child_module = strtolower($bits[1]);
                $target_controller = ucfirst($child_module);
                $target_controller_path = '../modules/' . $parent_module . '/' . $child_module . '/' . $target_controller . '.php';
                $target_module = $child_module;
            }

            if (!file_exists($target_controller_path)) {
                http_response_code(404);
                echo 'ERROR: Unable to locate ' . $target_module . ' module!';
                die();
            }
        }

        require_once $target_controller_path;
        $this->modules[$target_module] = new $target_controller($target_module);
    }

    /**
     * Lists all existing modules.
     *
     * @param bool $recursive Determines whether the listing should be recursive. Default is false.
     * @return array Returns an array containing the list of existing modules.
     */
    public function list(bool $recursive = false): array {
        $target_path = APPPATH . 'modules';
        $file = new File;
        $existing_modules = $file->list_directory($target_path, $recursive);
        return $existing_modules;
    }
}
