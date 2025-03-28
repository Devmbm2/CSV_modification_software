
$(document).ready(function () {
    // Handle Load Mapping Button Click
    $('#loadMappingButton').on('click', function () {
        // Clear previous content
        const modalBody = $('#loadMappingModal .modal-body');
        modalBody.html('<p>Loading mappings...</p>');

        // Show the modal
        $('#loadMappingModal').modal('show');

        // Fetch mappings via AJAX
        $.ajax({
            url: 'process.php', // Current PHP file
            type: 'POST',
            data: { action: 'fetch_mappings' },
            success: function (response) {
                try {
                    const result = JSON.parse(response);
                    if (result.status === 'success') {
                        const mappings = result.mappings;

                        // Build the mapping list
                        if (mappings.length === 0) {
                            modalBody.html('<p>No mappings available.</p>');
                        } else {
                            let mappingList = '<div class="container">'; // Wrap everything in a container for proper alignment
                            mappings.forEach(mapping => {
                                mappingList += `
                                    <div class="row align-items-center mb-3"> <!-- Row with vertical alignment and spacing -->
                                        <div class="col">${mapping}</div> <!-- Text column -->
                                        <div class="col-auto"> <!-- Button column (auto-width) -->
                                            <button class="btn btn-sm btn-primary load-mapping-btn" data-mapping-name="${mapping}">
                                                <i class="fas fa-upload me-1"></i> Load Mapping
                                            </button>
                                        </div>
                                    </div>`;
                            });
                            mappingList += '</div>'; // Close the container
                            modalBody.html(mappingList);

                            // Attach click event to Load Mapping buttons
                            $('.load-mapping-btn').off('click').on('click', function () {
                                const mappingName = $(this).data('mapping-name');

                                // Confirmation Dialog
                                if (confirm(`Do you want to remove your current mapping and load "${mappingName}"?`)) {
                                    // Send AJAX request to load the mapping
                                    $.ajax({
                                        url: 'process.php', // Current PHP file
                                        type: 'POST',
                                        data: { action: 'load_mapping', mapping_name: mappingName },
                                        success: function (response) {
                                            try {
                                                const result = JSON.parse(response);
                                                if (result.status === 'success') {
                                                    alert(result.message);
                                                    location.reload(); // Reload the page to reflect the new session
                                                } else {
                                                    alert(result.message);
                                                }
                                            } catch (e) {
                                                console.error("Invalid JSON response:", response);
                                                alert('An error occurred while processing your request.');
                                            }
                                        },
                                        error: function () {
                                            alert('An error occurred while processing your request.');
                                        }
                                    });
                                }
                            });
                        }
                    } else {
                        modalBody.html('<p>An error occurred while fetching mappings.</p>');
                    }
                } catch (e) {
                    console.error("Invalid JSON response:", response);
                    modalBody.html('<p>An error occurred while fetching mappings.</p>');
                }
            },
            error: function () {
                modalBody.html('<p>An error occurred while fetching mappings.</p>');
            }
        });
    });
});

$(document).ready(function () {
    $('#saveMappingForm').on('submit', function (e) {
        e.preventDefault(); // Prevent default form submission

        // Get the mapping name
        const mappingName = $('#mapping_name').val().trim();

        // Validate input
        if (!mappingName) {
            $('#ajaxResponse').html('<div class="alert alert-danger">Mapping name cannot be empty.</div>');
            return;
        }

        // Send AJAX request
        $.ajax({
            url: 'process.php', // Current PHP file
            type: 'POST',
            data: {
                action: 'save_mapping',
                mapping_name: mappingName
            },
            success: function (response) {
                const result = JSON.parse(response);

                // Display response message
                if (result.status === 'success') {
                    $('#ajaxResponse').html(`<div class="alert alert-success">${result.message}</div>`);
                    $('#saveMappingForm')[0].reset(); // Reset the form
                    setTimeout(() => $('#saveMappingModal').modal('hide'), 2000); // Close modal after 2 seconds
                } else {
                    $('#ajaxResponse').html(`<div class="alert alert-danger">${result.message}</div>`);
                }
            },
            error: function () {
                $('#ajaxResponse').html('<div class="alert alert-danger">An error occurred while processing your request.</div>');
            }
        });
    });
});