jQuery(document).ready(function($) {
    $('#add-book-form').on('submit', function(e) {
        e.preventDefault();

        var formData = {
            action: 'add_book_ajax',
            nonce: book_ajax.nonce,
            title: $('#book-title').val(),
            author: $('#book-author').val(),
            published_year: $('#book-year').val()
        };

        $.ajax({
            url: book_ajax.ajax_url,
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('#add-book-form button[type="submit"]').text('در حال افزودن...').prop('disabled', true);
            },
            success: function(response) {
                if (response.success) {

                    $('#book-messages').html('<div class="book-manager-message success">' + response.data.message + '</div>');

                    $('#add-book-form')[0].reset();

                    var newRow = '<tr>' +
                        '<td>' + response.data.row_number + '</td>' +
                        '<td>' + response.data.book.title + '</td>' +
                        '<td>' + response.data.book.author + '</td>' +
                        '<td>' + response.data.book.published_year + '</td>' +
                        '</tr>';

                    if ($('#books-table-body .no-books').length > 0) {
                        $('#books-table-body').empty();
                    }

                    $('#books-table-body').append(newRow);

                    setTimeout(function() {
                        $('#book-messages').empty();
                    }, 3000);
                } else {
                    $('#book-messages').html('<div class="book-manager-message error">' + response.data.message + '</div>');
                }
            },
            error: function() {
                $('#book-messages').html('<div class="book-manager-message error">خطا در ارسال اطلاعات. لطفاً دوباره تلاش کنید.</div>');
            },
            complete: function() {
                $('#add-book-form button[type="submit"]').text('اضافه کردن کتاب').prop('disabled', false);
            }
        });
    });
});