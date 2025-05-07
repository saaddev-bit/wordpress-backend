jQuery(document).ready(function($) {
    // Show edit form when clicking "Edit"
    $('.mfp-edit-project').on('click', function(e) {
        e.preventDefault();
        var $row = $(this).closest('tr');
        var projectId = $row.data('project-id');
        var projectUrl = $row.find('td:eq(1)').text();
        var clientName = $row.find('td:eq(2)').text();

        $('#mfp-project-id').val(projectId);
        $('#mfp-edit-project-url').val(projectUrl);
        $('#mfp-edit-client-name').val(clientName);
        $('#mfp-project-edit-form').show();
    });

    // Hide form on cancel
    $('#mfp-cancel-edit').on('click', function() {
        $('#mfp-project-edit-form').hide();
        $('#mfp-update-project-form')[0].reset();
    });

    // Handle form submission via AJAX
    $('#mfp-update-project-form').on('submit', function(e) {
        e.preventDefault();
        var projectId = $('#mfp-project-id').val();
        var projectUrl = $('#mfp-edit-project-url').val();
        var clientName = $('#mfp-edit-client-name').val();

        $.ajax({
            url: mfpAjax.ajaxurl,
            type: 'POST',
            data: {
                action: 'mfp_update_project',
                nonce: mfpAjax.nonce,
                project_id: projectId,
                project_url: projectUrl,
                client_name: clientName
            },
            beforeSend: function() {
                $('#mfp-update-project-form button[type="submit"]').text('Updating...');
            },
            success: function(response) {
                if (response.success) {
                    // Update the table row
                    var $row = $('tr[data-project-id="' + projectId + '"]');
                    $row.find('td:eq(1)').text(projectUrl);
                    $row.find('td:eq(2)').text(clientName);
                    $('#mfp-project-edit-form').hide();
                    $('#mfp-update-project-form')[0].reset();
                } else {
                    alert('Error: ' + response.data.message);
                }
            },
            error: function() {
                alert('An error occurred while updating the project.');
            },
            complete: function() {
                $('#mfp-update-project-form button[type="submit"]').text('Update Project');
            }
        });
    });
});