<?php

if (!defined('ABSPATH')) {
    exit;
}

class BookFormHandler {

    private $db;

    public function __construct() {
        $this->db = new BookDatabase();

        add_action('wp_ajax_add_book_ajax', array($this, 'ajax_add_book'));
        add_action('wp_ajax_nopriv_add_book_ajax', array($this, 'ajax_add_book'));
    }

    public function ajax_add_book() {
        check_ajax_referer('book_ajax_nonce', 'nonce');

        $title = isset($_POST['title']) ? trim($_POST['title']) : '';
        $author = isset($_POST['author']) ? trim($_POST['author']) : '';
        $year = isset($_POST['published_year']) ? intval($_POST['published_year']) : 0;

        $validation_result = $this->validate_form_data($title, $author, $year);

        if ($validation_result !== true) {
            wp_send_json_error(array('message' => $validation_result));
        }

        $result = $this->db->add_book($title, $author, $year);

        if ($result) {

            global $wpdb;
            $book_id = $wpdb->insert_id;
            $book = $this->db->get_book($book_id);

            $books = $this->db->get_all_books();
            $book_count = count($books);

            $row_number = $book_count;

            wp_send_json_success(array(
                'message' => __('کتاب با موفقیت اضافه شد.', 'book-manager'),
                'book' => $book,
                'row_number' => $row_number
            ));
        } else {
            wp_send_json_error(array('message' => __('خطا در اضافه کردن کتاب. لطفاً دوباره تلاش کنید.', 'book-manager')));
        }
    }

    public function process_form() {
        if (!isset($_POST['add_book']) || !isset($_POST['book_nonce'])) {
            return null;
        }

        if (!wp_verify_nonce($_POST['book_nonce'], 'add_book_nonce')) {
            return array(
                'type' => 'error',
                'text' => __('خطا در امنیت فرم. لطفاً دوباره تلاش کنید.', 'book-manager')
            );
        }

        $title = isset($_POST['book_title']) ? trim($_POST['book_title']) : '';
        $author = isset($_POST['book_author']) ? trim($_POST['book_author']) : '';
        $year = isset($_POST['book_year']) ? intval($_POST['book_year']) : 0;

        $validation_result = $this->validate_form_data($title, $author, $year);

        if ($validation_result !== true) {
            return array(
                'type' => 'error',
                'text' => $validation_result
            );
        }

        $result = $this->db->add_book($title, $author, $year);

        if ($result) {
            return array(
                'type' => 'success',
                'text' => __('کتاب با موفقیت اضافه شد.', 'book-manager')
            );
        } else {
            return array(
                'type' => 'error',
                'text' => __('خطا در اضافه کردن کتاب. لطفاً دوباره تلاش کنید.', 'book-manager')
            );
        }
    }

    private function validate_form_data($title, $author, $year) {

        if (empty($title)) {
            return __('عنوان کتاب الزامی است.', 'book-manager');
        }

        if (strlen($title) > 255) {
            return __('عنوان کتاب نمی‌تواند بیش از ۲۵۵ کاراکتر باشد.', 'book-manager');
        }

        if (empty($author)) {
            return __('نام نویسنده الزامی است.', 'book-manager');
        }

        if (strlen($author) > 255) {
            return __('نام نویسنده نمی‌تواند بیش از ۲۵۵ کاراکتر باشد.', 'book-manager');
        }

        if ($year < 1000) {
            return __('سال انتشار نامعتبر است.', 'book-manager');
        }

        if ($year > date('Y')) {
            return __('سال انتشار نمی‌تواند در آینده باشد.', 'book-manager');
        }

        return true;
    }

    public function sanitize_input($input) {
        return sanitize_text_field(trim($input));
    }
}