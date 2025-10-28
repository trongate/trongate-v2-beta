<?php

/**
 * Welcome Model
 *
 * Demonstrates multi-database functionality in Trongate v2.
 * This model can access both the default database and alternative database groups.
 */
class Welcome_model extends Model {

    /**
     * Get products from the default database (t2).
     * Uses $this->db to access the 'default' database group.
     *
     * @return array Array of product objects from the default database.
     */
    public function get_default_products(): array {
        // Access the default database
        $products = $this->db->get('id', 'products');
        return $products;
    }

    /**
     * Get products from the alternative database (df_web).
     * Uses $this->df_web to access the 'df_web' database group.
     *
     * @return array Array of product objects from the df_web database.
     */
    public function get_df_web_products(): array {
        // Access the df_web database
        $products = $this->df_web->get('id', 'products');
        return $products;
    }

    /**
     * Compare data from both databases.
     * Demonstrates accessing multiple database groups in a single method.
     *
     * @return array Associative array with comparison data.
     */
    public function compare_databases(): array {
        // Fetch from default database
        $default_products = $this->db->get('id', 'products');

        // Fetch from df_web database
        $df_web_products = $this->df_web->get('id', 'products');

        // Count records in each database
        $default_count = $this->db->count('products');
        $df_web_count = $this->df_web->count('products');

        return [
            'default_products' => $default_products,
            'df_web_products' => $df_web_products,
            'default_count' => $default_count,
            'df_web_count' => $df_web_count
        ];
    }

    /**
     * Get a specific product by ID from the default database.
     *
     * @param int $product_id The ID of the product.
     * @return object|false The product object or false if not found.
     */
    public function get_product_from_default(int $product_id): object|false {
        return $this->db->get_where($product_id, 'products');
    }

    /**
     * Get a specific product by ID from the df_web database.
     *
     * @param int $product_id The ID of the product.
     * @return object|false The product object or false if not found.
     */
    public function get_product_from_df_web(int $product_id): object|false {
        return $this->df_web->get_where($product_id, 'products');
    }

    /**
     * Get expensive products (price > 100) from default database.
     * Demonstrates using get_where_custom with the default database.
     *
     * @return array Array of expensive product objects.
     */
    public function get_expensive_products_default(): array {
        return $this->db->get_where_custom('price', 100, '>', 'price', 'products');
    }

    /**
     * Get expensive products (price > 100) from df_web database.
     * Demonstrates using get_where_custom with an alternative database.
     *
     * @return array Array of expensive product objects.
     */
    public function get_expensive_products_df_web(): array {
        return $this->df_web->get_where_custom('price', 100, '>', 'price', 'products');
    }
}
