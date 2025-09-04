<?php

if (!defined('ABSPATH')) {
    exit;
}

class BookShortcode {

    private $db;
    private $form_handler;

    public function __construct() {
        $this->db = new BookDatabase();
        $this->form_handler = new BookFormHandler();

        add_shortcode('book_list', array($this, 'display_book_list'));
    }

    public function display_book_list($atts) {
        ob_start();
        ?>
        <div class="book-manager-container">
            <div id="book-messages"></div>

            <div class="book-form-container">
                <h3><?php _e('اضافه کردن کتاب جدید', 'book-manager'); ?></h3>
                <form id="add-book-form" method="post" class="book-form">
                    <div class="form-row">
                        <label for="book-title"><?php _e('عنوان کتاب:', 'book-manager'); ?></label>
                        <input type="text" id="book-title" name="book_title" required>
                    </div>

                    <div class="form-row">
                        <label for="book-author"><?php _e('نویسنده:', 'book-manager'); ?></label>
                        <input type="text" id="book-author" name="book_author" required>
                    </div>

                    <div class="form-row">
                        <label for="book-year"><?php _e('سال انتشار:', 'book-manager'); ?></label>
                        <input type="number" id="book-year" name="book_year" min="1000" max="<?php echo date('Y'); ?>" required>
                    </div>

                    <div class="form-row">
                        <button type="submit" class="book-submit-btn">
                            <?php _e('اضافه کردن کتاب', 'book-manager'); ?>
                        </button>
                    </div>
                </form>
            </div>

            <div class="book-list-container">
                <h3><?php _e('لیست کتاب‌ها', 'book-manager'); ?></h3>
                <?php echo $this->render_books_table(); ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    private function render_books_table() {
        $books = $this->db->get_all_books();

        if (empty($books)) {
            return '<p class="no-books">' . __('هنوز کتابی اضافه نشده است.', 'book-manager') . '</p>';
        }

        ob_start();
        ?>
        <table class="book-table">
            <thead>
            <tr>
                <th><?php _e('شماره', 'book-manager'); ?></th>
                <th><?php _e('عنوان', 'book-manager'); ?></th>
                <th><?php _e('نویسنده', 'book-manager'); ?></th>
                <th><?php _e('سال انتشار', 'book-manager'); ?></th>
            </tr>
            </thead>
            <tbody id="books-table-body">
            <?php foreach ($books as $index => $book): ?>
                <tr>
                    <td><?php echo $index + 1; ?></td>
                    <td><?php echo esc_html($book['title']); ?></td>
                    <td><?php echo esc_html($book['author']); ?></td>
                    <td><?php echo esc_html($book['published_year']); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php
        return ob_get_clean();
    }
}