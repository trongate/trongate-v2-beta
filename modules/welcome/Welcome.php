<?php
class Welcome extends Trongate {

    /**
     * Renders the (default) homepage for public access.
     *
     * @return void
     */
    public function index(): void {
        $data['datetime'] = date('l jS \of F Y \a\t H:i');
        $this->view('welcome', $data);
    }

    public function test() {
        $this->view('test');
    }

    /**
     * Test method #1: Fetch data from the default database (t2)
     * This demonstrates accessing the primary database via the model.
     *
     * @return void
     */
    public function test1(): void {
        // Fetch products from the default database via model
        $products = $this->model->get_default_products();

        // Output as JSON for easy verification
        echo "<h1>Products from Default Database (t2)</h1>";
        echo "<pre>";
        print_r($products);
        echo "</pre>";

        // Also show as JSON
        echo "<h2>JSON Output:</h2>";
        echo "<pre>";
        echo json_encode($products, JSON_PRETTY_PRINT);
        echo "</pre>";
    }

    /**
     * Test method #2: Fetch data from the alternative database (df_web)
     * This demonstrates accessing an alternative database via the model.
     *
     * @return void
     */
    public function test2(): void {
        // Fetch products from the df_web database via model
        $products = $this->model->get_df_web_products();

        // Output as JSON for easy verification
        echo "<h1>Products from Alternative Database (df_web)</h1>";
        echo "<pre>";
        print_r($products);
        echo "</pre>";

        // Also show as JSON
        echo "<h2>JSON Output:</h2>";
        echo "<pre>";
        echo json_encode($products, JSON_PRETTY_PRINT);
        echo "</pre>";
    }

    /**
     * BONUS: Test method #3: Compare data from both databases
     * This demonstrates accessing multiple databases in a single model method.
     *
     * @return void
     */
    public function test3(): void {
        // Get comparison data from model
        $comparison = $this->model->compare_databases();

        echo "<h1>Database Comparison</h1>";

        echo "<h2>Default Database (t2) Products:</h2>";
        echo "<pre>";
        print_r($comparison['default_products']);
        echo "</pre>";

        echo "<h2>Alternative Database (df_web) Products:</h2>";
        echo "<pre>";
        print_r($comparison['df_web_products']);
        echo "</pre>";

        echo "<h2>Summary:</h2>";
        echo "<ul>";
        echo "<li>Total products in default database: " . $comparison['default_count'] . "</li>";
        echo "<li>Total products in df_web database: " . $comparison['df_web_count'] . "</li>";
        echo "<li>Combined total: " . ($comparison['default_count'] + $comparison['df_web_count']) . "</li>";
        echo "</ul>";
    }

    public function debug_databases() {
        echo "<h1>Database Configuration Debug</h1>";

        echo "<h2>Check if \$GLOBALS['databases'] exists:</h2>";
        if (isset($GLOBALS['databases'])) {
            echo "<p style='color: green;'>✅ YES</p>";
            echo "<pre>";
            print_r($GLOBALS['databases']);
            echo "</pre>";
        } else {
            echo "<p style='color: red;'>❌ NO - This is the problem!</p>";
        }

        echo "<h2>Check if 'df_web' key exists:</h2>";
        if (isset($GLOBALS['databases']['df_web'])) {
            echo "<p style='color: green;'>✅ YES</p>";
        } else {
            echo "<p style='color: red;'>❌ NO - This is why Model::df_web fails!</p>";
        }
    }

}
