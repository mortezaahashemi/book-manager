<?php

if (!defined('ABSPATH')) {
    exit;
}

class BookDatabase {

    private $table_name;

    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'books';
    }

    public function create_table() {
        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $this->table_name (
            id int(11) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            author varchar(255) NOT NULL,
            published_year int(4) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        add_option('book_manager_version', '1.0.0');
    }

    public function add_book($title, $author, $published_year) {
        global $wpdb;

        $result = $wpdb->insert(
            $this->table_name,
            array(
                'title' => sanitize_text_field($title),
                'author' => sanitize_text_field($author),
                'published_year' => intval($published_year)
            ),
            array('%s', '%s', '%d')
        );

        return $result !== false;
    }

    public function get_all_books() {
        global $wpdb;

        $results = $wpdb->get_results(
            "SELECT * FROM $this->table_name ORDER BY id ASC",
            ARRAY_A
        );

        return $results ? $results : array();
    }

    public function get_book($id) {
        global $wpdb;

        $result = $wpdb->get_row(
            $wpdb->prepare("SELECT * FROM $this->table_name WHERE id = %d", $id),
            ARRAY_A
        );

        return $result;
    }

    public function table_exists() {
        global $wpdb;

        $table_name = $wpdb->get_var($wpdb->prepare(
            "SHOW TABLES LIKE %s",
            $this->table_name
        ));

        return $table_name === $this->table_name;
    }
}