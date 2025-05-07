jQuery(document).ready(function($) {
    $('.mfp-load-more').on('click', function(e) {
        e.preventDefault();
        var $button = $(this);
        var $container = $button.closest('.mfp-projects');
        var category = $container.data('category');
        var limit = $container.data('limit');
        var paged = parseInt($container.data('paged')) + 1;

        $.ajax({
            url: mfpAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'mfp_load_more_projects',
                nonce: mfpAjax.nonce,
                category: category,
                limit: limit,
                paged: paged
            },
            beforeSend: function() {
                $button.text('Loading...');
            },
            success: function(response) {
                $container.data('paged', paged);
                $container.find('.project').last().after(response);
                $button.text('Load More');

                // Check if there are more pages to load
                if (paged >= mfpAjax.maxPages) {
                    $button.remove();
                }
            },
            error: function() {
                $button.text('Error loading projects');
            }
        });
    });
});