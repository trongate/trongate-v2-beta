<?php
class Welcome extends Trongate {

    public function ahoy() {
        $this->view('ahoy');
    }

    public function icons() {

        $icon_dir = APPPATH . '/public/trongate-icons';
        $data['icons'] = glob($icon_dir . '/*.svg');
        $this->view('icons', $data);


    }

    public function test() {
        $rows = $this->db->get('chapter_number', 'documentation_chapters');
        $counter = 0;
        foreach($rows as $row) {
            
            if ($row->book_id === 1) {
                $counter++;
                echo $counter.': '.$row->chapter_title.',<br>';
            }
            
        }
    }

/*
CREATE TABLE users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(100) NOT NULL,
  last_name VARCHAR(100) NOT NULL,
  email_address VARCHAR(255) NOT NULL
);

INSERT INTO users (first_name, last_name, email_address)
VALUES ('Alice', 'Sutherland', 'alice.sutherland@example.com');

INSERT INTO users (first_name, last_name, email_address)
VALUES ('Brian', 'McDowell', 'brian.mcdowell@example.com');

INSERT INTO users (first_name, last_name, email_address)
VALUES ('Caroline', 'Walker', 'caroline.walker@example.com');

*/

    public function demo() {
        $this->view('demo');
    }

    /**
     * Renders the (default) homepage for public access.
     *
     * @return void
     */
    public function index(): void {
        $this->view('stealth_homepage');
    }

    public function render_latest_version() {
        echo 'Latest: <a href="'.GITHUB_URL.'" target="_blank">1.3.3063</a>';
    }

    public function learn_trongate() {
        $data['resources'] = [
            [
                'title' => 'Documentation',
                'url' => 'docs',
                'description' => 'Dive into the official Trongate documentation - a clear, example-rich guide that walks you through every part of the framework. From setup to advanced concepts, you’ll find practical explanations designed to help you master Trongate with confidence.'
            ],
            [
                'title' => 'Discussion Forums',
                'url' => 'https://trongate.io/forums',
                'description' => 'Join the Trongate discussion forums and get help from fellow Trongate developers. Ask questions, share solutions, or explore tips and tricks - the community is here to support you every step of the way.'
            ],
            [
                'title' => 'Complete API Reference Guide',
                'url' => 'https://trongate.io/documentation/reference',
                'description' => 'Every module, function, and helper - all in one place! This exhaustive reference guide helps you unlock the full potential of the Trongate framework with ease.'
            ]
        ];

        $this->view('learn_trongate', $data);
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
