const id = "#kt_modal_upload_dropzone";
const dropzone = document.querySelector(id);

var previewNode = dropzone.querySelector(".dropzone-item");
previewNode.id = "";
var previewTemplate = previewNode.parentNode.innerHTML;
previewNode.parentNode.removeChild(previewNode);

var myDropzone = new Dropzone(id, {
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    url: "{{ route('upload.file') }}",
    parallelUploads: 20,
    previewTemplate: previewTemplate,
    maxFilesize: 1,
    autoQueue: false,
    previewsContainer: id + " .dropzone-items",
    clickable: id + " .dropzone-select"
});

myDropzone.on("addedfile", function (file) {
    // Hookup the start button
    file.previewElement.querySelector(id + " .dropzone-start").onclick = function () {
        myDropzone.enqueueFile(file);
    };
    const dropzoneItems = dropzone.querySelectorAll('.dropzone-item');
    dropzoneItems.forEach(dropzoneItem => {
        dropzoneItem.style.display = '';
    });
    dropzone.querySelector('.dropzone-upload').style.display = "inline-block";
    dropzone.querySelector('.dropzone-remove-all').style.display = "inline-block";
});

// Update the total progress bar
myDropzone.on("totaluploadprogress", function (progress) {
    const progressBars = dropzone.querySelectorAll('.progress-bar');
    progressBars.forEach(progressBar => {
        progressBar.style.width = progress + "%";
    });
});

myDropzone.on("sending", function (file) {
    // Show the total progress bar when upload starts
    const progressBars = dropzone.querySelectorAll('.progress-bar');
    progressBars.forEach(progressBar => {
        progressBar.style.opacity = "1";
    });
    // And disable the start button
    file.previewElement.querySelector(id + " .dropzone-start").setAttribute("disabled", "disabled");
});

// Hide the total progress bar when nothing's uploading anymore
myDropzone.on("complete", function (progress) {
    const progressBars = dropzone.querySelectorAll('.dz-complete');

    setTimeout(function () {
        progressBars.forEach(progressBar => {
            progressBar.querySelector('.progress-bar').style.opacity = "0";
            progressBar.querySelector('.progress').style.opacity = "0";
            progressBar.querySelector('.dropzone-start').style.opacity = "0";
        });
    }, 300);

    location.reload();
});

// Setup the buttons for all transfers
dropzone.querySelector(".dropzone-upload").addEventListener('click', function () {
    myDropzone.enqueueFiles(myDropzone.getFilesWithStatus(Dropzone.ADDED));
});

// Setup the button for remove all files
dropzone.querySelector(".dropzone-remove-all").addEventListener('click', function () {
    dropzone.querySelector('.dropzone-upload').style.display = "none";
    dropzone.querySelector('.dropzone-remove-all').style.display = "none";
    myDropzone.removeAllFiles(true);
});

// On all files completed upload
myDropzone.on("queuecomplete", function (progress) {
    const uploadIcons = dropzone.querySelectorAll('.dropzone-upload');
    uploadIcons.forEach(uploadIcon => {
        uploadIcon.style.display = "none";
    });
});

// On all files removed
myDropzone.on("removedfile", function (file) {
    if (myDropzone.files.length < 1) {
        dropzone.querySelector('.dropzone-upload').style.display = "none";
        dropzone.querySelector('.dropzone-remove-all').style.display = "none";
    }
});

$(document).ready(function () {
    // Rename button click handler
    $(document).on('click', '#kt_file_manager_rename_folder', function (e) {
        e.preventDefault();
        e.stopPropagation();

        // Get the new folder name from the input field
        var newFolderName = $('#kt_file_manager_rename_input').val().trim();
        if (newFolderName === '') {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Please enter a valid folder name.',
            });
            return;
        }

        // Assume data-folder-id attribute holds the folder's ID
        var folderId = $(this).closest('tr').data('folder-id');

        // SweetAlert confirmation before renaming
        Swal.fire({
            title: 'Are you sure?',
            text: `Do you want to rename this folder to "${newFolderName}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, rename it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Perform AJAX request to rename the folder
                $.ajax({
                    url: `/folder/rename/${folderId}`, // Replace with your route
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}', // CSRF token for security
                        folder_id: folderId,
                        new_folder_name: newFolderName
                    },
                    success: function (response) {
                        if (response.success) {
                            // Update the UI with the new folder name
                            $(`tr[data-folder-id="${folderId}"] td a`).text(
                                newFolderName);

                            // Hide the rename input
                            $('#kt_file_manager_rename_template').addClass(
                                'd-none');

                            Swal.fire({
                                title: 'Renamed!',
                                text: 'Your folder has been renamed successfully.',
                                icon: 'success',
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Reload the page
                                    location.reload();
                                }
                            });

                        } else {
                            Swal.fire(
                                'Error',
                                'Error renaming folder: ' + response
                                .message,
                                'error'
                            );
                        }
                    },
                    error: function (xhr) {
                        Swal.fire(
                            'Error',
                            'An error occurred while renaming the folder.',
                            'error'
                        );
                    }
                });
            }
        });
    });

    // Cancel rename button click handler
    $(document).on('click', '#kt_file_manager_rename_folder_cancel', function (e) {
        e.preventDefault();
        e.stopPropagation();

        // // Hide the rename input
        $('#kt_file_manager_rename_template').addClass('d-none');

        $('.modal-backdrop').remove();

        // Reset modal state
        $('#kt_modal_upload').find('form').trigger('reset');
    });

    // Trigger showing the rename template (e.g., on a rename icon click next to a folder)
    $(document).on('click', '.rename-folder-icon', function (e) {
        e.preventDefault();
        e.stopPropagation();

        // Show the rename input
        $('#kt_file_manager_rename_template').removeClass('d-none');

        // Prefill the input with the current folder name
        var currentFolderName = $(this).closest('tr').find('a').text().trim();
        $('#kt_file_manager_rename_input').val(currentFolderName);

        // Set the data-folder-id attribute to the current folder's ID
        var folderId = $(this).closest('tr').data('folder-id');
        $('#kt_file_manager_rename_template').data('folder-id', folderId);
    });


    // Handle "Select All" checkbox
    $('#select_all').on('change', function () {
        var isChecked = $(this).is(':checked');
        $('.file-manager-checkbox').prop('checked', isChecked);
        updateSelectedCount();
    });

    // Handle individual row checkbox changes
    $(document).on('change', '.file-manager-checkbox', function () {
        var allChecked = $('.file-manager-checkbox').length === $('.file-manager-checkbox:checked').length;
        $('#select_all').prop('checked', allChecked);
        updateSelectedCount();
    });

    // Update selected count and show toolbar
    function updateSelectedCount() {
        var selectedCount = $('.file-manager-checkbox:checked').length;
        if (selectedCount > 0) {
            $('[data-kt-filemanager-table-toolbar="selected"]').removeClass('d-none');
            $('[data-kt-filemanager-table-select="selected_count"]').text(selectedCount);
        } else {
            $('[data-kt-filemanager-table-toolbar="selected"]').addClass('d-none');
        }
    }

    // Handle "Delete Selected" button click
    $('#kt_file_manager_delete_selected').on('click', function () {
        var selectedFolders = $('.file-manager-checkbox:checked[data-type="folder"]').map(function () {
            return $(this).val();
        }).get();

        var selectedDocuments = $('.file-manager-checkbox:checked[data-type="document"]').map(function () {
            return $(this).val();
        }).get();

        if (selectedFolders.length === 0 && selectedDocuments.length === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Selection',
                text: 'No items selected for deletion.',
                confirmButtonText: 'OK'
            });
            return;
        }

        // Confirm deletion using SweetAlert
        Swal.fire({
            title: 'Are you sure?',
            text: 'You won\'t be able to revert this!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'No, cancel!',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Handle folder deletion
                if (selectedFolders.length > 0) {
                    $.ajax({
                        url: '/folder/delete-selected',
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            ids: selectedFolders,
                            type: 'folder'
                        },
                        success: function (response) {
                            if (response.success) {
                                $('.file-manager-checkbox:checked[data-type="folder"]').closest('tr').remove();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Selected folders have been deleted.',
                                    confirmButtonText: 'OK'
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Error deleting selected folders: ' + response.message,
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while deleting selected folders.',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }

                // Handle document deletion
                if (selectedDocuments.length > 0) {
                    $.ajax({
                        url: '/document/delete-selected',
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            ids: selectedDocuments,
                            type: 'document'
                        },
                        success: function (response) {
                            if (response.success) {
                                $('.file-manager-checkbox:checked[data-type="document"]').closest('tr').remove();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: 'Selected documents have been deleted.',
                                    confirmButtonText: 'OK'
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Error deleting selected documents: ' + response.message,
                                    confirmButtonText: 'OK'
                                });
                            }
                        },
                        error: function (xhr) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'An error occurred while deleting selected documents.',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }

                // Hide the toolbar
                $('[data-kt-filemanager-table-toolbar="selected"]').addClass('d-none');
            }
        });
    });

    $(document).on('click', '#kt_file_manager_cancel_button', function (e) {
        e.preventDefault();
        e.stopPropagation();

        // Clear the input field
        $('input[name="new_folder_name"]').val('');

        // Hide the new folder row
        $('#kt_file_manager_new_folder_row').addClass('d-none');

        // Properly hide the modal (if using modal)
        $('#kt_modal_upload').modal('hide');

        // Ensure any lingering modal backdrops are removed
        $('.modal-backdrop').remove();

        // Reset modal state
        $('#kt_modal_upload').find('form').trigger('reset');
    });

    $(document).on('click', '#kt_file_manager_new_folder', function (e) {
        e.preventDefault();
        e.stopPropagation();

        // Show the new folder row
        $('#kt_file_manager_new_folder_row').removeClass('d-none');
    });

    $(document).on('click', '[data-bs-toggle="modal"]', function () {
        // Ensure modal is reset before showing
        var targetModal = $($(this).data('bs-target'));
        targetModal.modal('hide');
        targetModal.modal('show');
    });

    // Handle delete button click
    $(document).on('click', '[data-kt-filemanager-table-filter="delete_row"]', function (e) {
        e.preventDefault();

        var folderId = $(this).data('folder-id');
        var row = $(this).closest('tr'); // Get the row to remove after deletion

        // SweetAlert confirmation dialog
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Proceed with deletion
                $.ajax({
                    url: '/folder/delete/' + folderId,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr(
                            'content'), // Include CSRF token
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire(
                                'Deleted!',
                                'The folder has been deleted.',
                                'success'
                            );
                            row
                                .remove(); // Remove the folder's row from the table
                        } else {
                            Swal.fire(
                                'Error!',
                                'Error deleting folder: ' + response
                                .message,
                                'error'
                            );
                        }
                    },
                    error: function (xhr) {
                        Swal.fire(
                            'Error!',
                            'An error occurred while deleting the folder.',
                            'error'
                        );
                    }
                });
            }
        });
    });

    // Handle delete button click
    $(document).on('click', '[data-kt-filemanager-table-filter="delete_row_document"]', function (e) {
        e.preventDefault();

        var documentId = $(this).data('document-id');
        var row = $(this).closest('tr'); // Get the row to remove after deletion

        // SweetAlert confirmation dialog
        Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                // Proceed with deletion
                $.ajax({
                    url: '/delete/file/' + documentId,
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr(
                            'content'), // Include CSRF token
                    },
                    success: function (response) {
                        if (response.success) {
                            Swal.fire(
                                'Deleted!',
                                'The document has been deleted.',
                                'success'
                            );
                            row
                                .remove(); // Remove the folder's row from the table
                        } else {
                            Swal.fire(
                                'Error!',
                                'Error deleting folder: ' + response
                                .message,
                                'error'
                            );
                        }
                    },
                    error: function (xhr) {
                        Swal.fire(
                            'Error!',
                            'An error occurred while deleting the folder.',
                            'error'
                        );
                    }
                });
            }
        });
    });
});
