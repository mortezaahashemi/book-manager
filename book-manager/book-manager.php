<?php
/**
 * Plugin Name: Book Manager
 * Plugin URI: https://MortezaHashemi.ir
 * Description: A simple plugin for managing books in WordPress
 * Version: 1.0.0
 * Author: Seyed Morteza Hashemi
 * License: GPL2
 * Text Domain: book-manager
 */

if (!defined('ABSPATH')) {
    exit;
}

define('BOOK_MANAGER_PLUGIN_URL', plugin_dir_url(__FILE__));
define('BOOK_MANAGER_PLUGIN_PATH', plugin_dir_path(__FILE__));

require_once BOOK_MANAGER_PLUGIN_PATH . 'includes/class-book-database.php';
require_once BOOK_MANAGER_PLUGIN_PATH . 'includes/class-book-shortcode.php';
require_once BOOK_MANAGER_PLUGIN_PATH . 'includes/class-book-form-handler.php';

class BookManager {

    private $db;
    private $shortcode;
    private $form_handler;

    public function __construct() {
        $this->db = new BookDatabase();
        $this->shortcode = new BookShortcode();
        $this->form_handler = new BookFormHandler();

        $this->init_hooks();
    }

    private function init_hooks() {

        register_activation_hook(__FILE__, array($this->db, 'create_table'));

        register_deactivation_hook(__FILE__, array($this, 'deactivate'));

        register_uninstall_hook(__FILE__, array('BookManager', 'uninstall'));

        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    public function init() {
        load_plugin_textdomain('book-manager', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }

    public function enqueue_scripts() {
        wp_enqueue_style('vazirmatn-variable-font', BOOK_MANAGER_PLUGIN_URL . 'assets/fonts/vazirmatn-variable.css', array(), '1.0.0');
        wp_enqueue_style('book-manager-style', BOOK_MANAGER_PLUGIN_URL . 'assets/style.css', array('vazirmatn-variable-font'), '1.0.0');
        wp_enqueue_script('book-manager-ajax', BOOK_MANAGER_PLUGIN_URL . 'assets/book-manager-ajax.js', array('jquery'), '1.0.0', true);
        wp_localize_script('book-manager-ajax', 'book_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('book_ajax_nonce')
        ));
    }

    public function deactivate() {}

    public static function uninstall() {
        global $wpdb;

        $table_name = $wpdb->prefix . 'books';
        $wpdb->query("DROP TABLE IF EXISTS $table_name");

        delete_option('book_manager_version');
    }
}

new BookManager();