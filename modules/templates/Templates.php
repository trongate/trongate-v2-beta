<?php
class Templates extends Trongate {
    
    /**
     * Constructor - Prevent direct URL access to this module
     */
    public function __construct() {
       
        // Prevent direct URL access
        if (segment(1) === 'templates') {
            http_response_code(404);
            echo '<!DOCTYPE html><html><head><title>404 Not Found</title></head><body>';
            echo '<h1>404 - Page Not Found</h1>';
            echo '<p>The page you are looking for could not be found.</p>';
            echo '<p><a href="' . BASE_URL . '">Return to homepage</a></p>';
            echo '</body></html>';
            exit;
        }
    }
    
    public function admin(array $data): void {
        
        // Default view_file if not provided
        if (!isset($data['view_file'])) {
            $data['view_file'] = 'index';
        }
        
        // Display the admin template
        $this->display('admin', $data);
    }
    
    public function public(array $data): void {
        
        if (!isset($data['view_file'])) {
            $data['view_file'] = 'index';
        }
        
        $this->display('public', $data);
    }

/**
 * Display 404 error page.
 *
 * @return void
 */
public function error_404(): void {
    http_response_code(404);
    $this->display('error_404');
}
    
/**
 * Display a template file from this module
 */
private function display(string $template_name, array $data = []): void {
    $template_path = __DIR__ . "/views/{$template_name}.php";
    
    if (!file_exists($template_path)) {
        throw new Exception("Template '{$template_name}' not found at {$template_path}");
    }
    
    extract($data);
    require $template_path;
}

}