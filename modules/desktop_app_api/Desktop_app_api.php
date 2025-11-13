<?php
class Desktop_app_api extends Trongate {

    private $codegen_templates = ['c64'];

	public function __construct() {
	    // CORS: Let everyone in
	    header('Access-Control-Allow-Origin: *');
	    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
	    header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, trongate-mx-request, X-Window-Type');
	    // Handle preflight (OPTIONS) requests
	    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	        http_response_code(204);
	        exit;
	    }
	}

    /**
     * Display the home page with navigation menu.
     *
     * Renders the main navigation menu containing links to various code generator
     * sections including module creation, sample forms, and interactive components.
     * Supports optional template wrapping via query parameter.
     *
     * @return void
     */
    public function home(): void {
        $this->render_with_optional_template('home');
    }

    /**
     * Display the module creation options page.
     *
     * Renders an interface allowing users to choose between creating a new module
     * from scratch or obtaining a pre-built module from the Module Market.
     * Supports optional template wrapping via query parameter.
     *
     * @return void
     */
    public function create_new_mod(): void {
        $this->render_with_optional_template('create_new_mod');
    }

    /**
     * Display module type selection options.
     *
     * Renders a selection menu where users can choose between creating a new
     * Trongate module or browsing the Module Market for pre-built modules.
     * Supports optional template wrapping via query parameter.
     *
     * @return void
     */
    public function choose_mod_type(): void {
        $this->render_with_optional_template('choose_mod_type');
    }

    /**
     * Display the module name input form.
     *
     * Renders a form allowing users to enter a name for their new Trongate module.
     * The form submits to the submit_mod_name endpoint for validation and processing.
     * Supports optional template wrapping via query parameter.
     *
     * @return void
     */
    public function enter_mod_name(): void {
        $this->render_with_optional_template('enter_mod_name');
    }

    /**
     * Submit and validate a new module name
     *
     * Validates the submitted module name against length, format, and conflict requirements.
     * Returns immediately upon encountering the first validation error.
     *
     * @return void
     */
    public function submit_mod_name(): void {

        $mod_name = post('mod_name', true);
        $existing_modules = post('existing_modules') ?? [];
        $plural = $this->plural_maker->_get_plural($mod_name);

        // Validate minimum length
        if (strlen($mod_name) < 2) {
            $this->return_error('Module name must be at least 2 characters long.');
        }

        // Validate maximum length
        if (strlen($mod_name) > 50) {
            $this->return_error('Module name cannot exceed 50 characters.');
        }

        // Validate format (letters, numbers, underscores, and spaces only - must start with letter)
        if (!preg_match('/^[a-zA-Z][a-zA-Z0-9_ ]*$/', $mod_name)) {
            $this->return_error('Module name must start with a letter and contain only letters, numbers, underscores, and spaces.');
        }

        // Check for module assets trigger
        if (strpos($mod_name, MODULE_ASSETS_TRIGGER) !== false) {
            $this->return_error('Module name cannot contain "' . MODULE_ASSETS_TRIGGER . '".');
        }

        // Check for reserved names
        $reserved_names = ['config', 'system', 'engine', 'public', 'templates', 'assets', 'modules', 'welcome'];
        if (in_array(strtolower($mod_name), $reserved_names)) {
            $this->return_error('This module name is reserved and cannot be used.');
        }

        // Check for conflicts with existing modules
        if (!empty($existing_modules)) {
            if (in_array($plural, $existing_modules)) {
                $this->return_error("A module named '{$plural}' already exists.");
            }
        }

        $module_folder_name = url_title($plural);
        $module_folder_name = str_replace('-', '_', $module_folder_name);

        // All validation passed - continue with module creation...
        $response['storageItemsToAdd'] = [
            'record_name_singular' => $mod_name,
            'record_name_plural' => $plural,
            'module_folder_name' => $module_folder_name
        ];

        $response['nextRequest'] = [
            'targetUrl' => 'desktop_app_api/add_nav_label',
            'requestType' => 'get',
            'additionalMXAttributes' => [
                'mx-after-swap' => 'TrongateCodeGenerator.focusOnInput'
            ]
        ];

        http_response_code(200);
        echo '<div class="cloak api-response">'.json_encode($response).'</div>';
    }

    /**
     * Display the navigation label input form.
     *
     * Renders a form allowing users to enter an optional navigation menu label
     * for a new module being generated. The label will be used to identify the
     * module in the application's navigation menu.
     * Supports optional template wrapping via query parameter.
     *
     * @return void
     */
    public function add_nav_label(): void {
        $this->render_with_optional_template('add_nav_label');
    }

    /**
     * Process the submitted navigation label for a new module.
     *
     * Handles the navigation label submission from the add_nav_label form.
     * If the label is empty, stores empty navigation values in localStorage and
     * proceeds directly to the module properties configuration stage. If a valid
     * label is provided, stores the label in localStorage and proceeds to the
     * navigation icon selection interface.
     *
     * @return void
     */
    public function submit_nav_label(): void {
        $nav_label = post('nav_label', true);

        // Determine the next stage based on whether a nav label was provided
        $target_url = ($nav_label === '')
            ? 'desktop_app_api/lets_add_properties_conf'
            : 'desktop_app_api/conf_add_nav_icon';

        $response = [
            'storageItemsToAdd' => [
                'nav_label' => $nav_label,
                'icon_code' => '',
                'icon_id' => ''
            ],
            'nextRequest' => [
                'targetUrl' => $target_url,
                'requestType' => 'get'
            ]
        ];

        http_response_code(200);
        echo '<div class="cloak api-response">'.json_encode($response).'</div>';
    }

    /**
     * Display the module properties configuration confirmation page.
     *
     * Renders a confirmation interface prompting users to proceed with adding
     * properties to their new module. Provides a button that opens the properties
     * builder interface in an expanded modal (1600x900px).
     * Supports optional template wrapping via query parameter.
     *
     * @return void
     */
    public function lets_add_properties_conf(): void {
        $this->render_with_optional_template('lets_add_properties_conf');
    }

    /**
     * Renders the Properties Builder webpage.
     *
     * This method loads the 'properties_builder_da' template with the
     * 'properties_builder' view file. The page allows users to choose
     * properties for modules that are to be generated.
     *
     * @return void
     */
    public function properties_builder(): void {
        $data['view_file'] = 'properties_builder';
        $this->template('properties_builder_da', $data);
    }

    /**
     * Display the URL column (slug) selection interface.
     *
     * Renders a form allowing users to optionally select one of their declared
     * module properties to serve as a URL column (slug) for the database table.
     * This is presented after the user has finished defining their module properties.
     * Users can submit without selecting a column if a URL column is not required
     * for their module.
     * Supports optional template wrapping via query parameter.
     *
     * @return void
     */
    public function choose_url_col(): void {
        $this->render_with_optional_template('choose_url_col');
    }

    /**
     * Handle the submission of the selected URL column from the frontend.
     *
     * This method retrieves the selected column via a POST request, validates it,
     * and returns a JSON response wrapped in a <div> with class "cloak api-response".
     *
     * @return void
     */
    public function submit_url_col(): void {
        // Retrieve the 'selected' value from POST and sanitize it
        $selected = post('selected', true);

        // Prepare items to store in localStorage (or for the frontend)
        $response['storageItemsToAdd'] = [
            'urlColumn' => $selected
        ];

        // Prepare next request instruction for the frontend
        $response['nextRequest'] = [
            'targetUrl'   => 'desktop_app_api/choose_order_by',
            'requestType' => 'get'
        ];

        // Send HTTP 200 response code
        http_response_code(200);

        // Output JSON response wrapped in a cloak div
        echo '<div class="cloak api-response">' . json_encode($response) . '</div>';
    }

    /**
     * Display the "Order By" selection interface for the module.
     *
     * Renders a form allowing users to select the property by which records
     * should be ordered in the module's database table. This step occurs after
     * the URL column has been selected (if any) and module properties have been defined.
     * Users can optionally choose an ordering preference or skip this step.
     * Supports optional template wrapping via query parameter.
     *
     * @return void
     */
    public function choose_order_by(): void {
        $this->render_with_optional_template('choose_order_by');
    }

    /**
     * Handle the submission of the selected URL column from the frontend.
     *
     * This method retrieves the selected column via a POST request, validates it,
     * and returns a JSON response wrapped in a <div> with class "cloak api-response".
     *
     * @return void
     */
    public function submit_order_by(): void {
        // Retrieve the 'selected' value from POST and sanitize it
        $selected = post('selected', true);

        // Prepare items to store in localStorage (or for the frontend)
        $response['storageItemsToAdd'] = [
            'orderBy' => $selected
        ];

        // Prepare next request instruction for the frontend
        $response['nextRequest'] = [
            'targetUrl'   => 'desktop_app_api/conf_generate_mod',
            'requestType' => 'get'
        ];

        // Send HTTP 200 response code
        http_response_code(200);

        // Output JSON response wrapped in a cloak div
        echo '<div class="cloak api-response">' . json_encode($response) . '</div>';
    }

    /**
     * Renders the module generation confirmation page.
     *
     * This method loads the 'conf_generate_mod' view using an optional template.
     * It displays a confirmation page that appears before a module is generated.
     *
     * @return void
     */
    public function conf_generate_mod(): void {
        $this->render_with_optional_template('conf_generate_mod');
    }

    /**
     * Render a view with an optional template wrapper.
     *
     * If a 'template' query parameter is provided, the view will be wrapped
     * in the specified template. Otherwise, the view is rendered directly.
     *
     * @param string $view_file The name of the view file to render
     * @return void
     */
    private function render_with_optional_template(string $view_file): void {
        $template = $_GET['template'] ?? '';

        if ($template !== '') {

            if (!in_array($template, $this->codegen_templates)) {
                http_response_code(403);
                echo 'Invalid template value.';
                die();
            }

            $data['view_file'] = $view_file;
            $this->template($template, $data);
        } else {
            $this->view($view_file);
        }
    }

}