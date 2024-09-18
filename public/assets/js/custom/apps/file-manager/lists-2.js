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

        // Get folder ID from the row's data attribute
        var folderId = $(this).closest('tr').data('folder-id');

        console.log(folderId);

        // SweetAlert confirmation dialog
        Swal.fire({
            text: `Do you want to rename this folder to "${newFolderName}"?`,
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: "Yes, rename!",
            cancelButtonText: "No, cancel",
            customClass: {
                confirmButton: "btn fw-bold btn-danger",
                cancelButton: "btn fw-bold btn-active-light-primary"
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Perform AJAX request to rename the folder
                $.ajax({
                    url: `/admin/folder-rename/` + folderId, // Replace with your route
                    type: 'POST',
                    data: {
                        _token: $('meta[name="csrf-token"]').attr(
                            'content'),
                        folder_id: folderId,
                        new_folder_name: newFolderName
                    },
                    success: function (response) {
                        if (response.success) {
                            // Update the UI with the new folder name
                            $(`tr[data-folder-id="${folderId}"] td a`).text(
                                newFolderName);

                            // Hide the rename input template
                            $('#kt_file_manager_rename_template').addClass(
                                'd-none');

                            // Show success message with Toastr
                            Swal.fire({
                                text: `You have renamed the folder to "${newFolderName}"!`,
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            }).then(function () {
                                location.reload();
                            });

                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: `Error renaming folder: ${response.message}`,
                            });
                        }
                    },
                    error: function (xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while renaming the folder.',
                        });
                    }
                });
            }
        });
    });

    // Handle "Select All" checkbox
    $('#select_all').on('change', function () {
        var isChecked = $(this).is(':checked');
        $('.file-manager-checkbox').prop('checked', isChecked);
    });

    // Handle individual row checkbox changes
    $(document).on('change', '.file-manager-checkbox', function () {
        var allChecked = $('.file-manager-checkbox').length === $(
            '.file-manager-checkbox:checked').length;
        $('#select_all').prop('checked', allChecked);
    });



    // Handle "Delete Selected" button click
    $('#kt_file_manager_delete_selected').on('click', function () {
        var selectedFolders = $('.file-manager-checkbox:checked[data-type="folder"]').map(
            function () {
                return $(this).val();
            }).get();

        var selectedDocuments = $('.file-manager-checkbox:checked[data-type="document"]').map(
            function () {
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
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: "Yes, delete!",
            cancelButtonText: "No, cancel",
            customClass: {
                confirmButton: "btn fw-bold btn-danger",
                cancelButton: "btn fw-bold btn-active-light-primary"
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Handle folder deletion
                if (selectedFolders.length > 0) {
                    $.ajax({
                        url: '/admin/folder/delete-selected',
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr(
                                'content'),
                            ids: selectedFolders,
                            type: 'folder'
                        },
                        success: function (response) {
                            if (response.success) {
                                $('.file-manager-checkbox:checked[data-type="folder"]')
                                    .closest('tr').remove();
                                Swal.fire({
                                    icon: "success",
                                    text: response.message,
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary",
                                    }
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    buttonsStyling: false,
                                    text: 'Error deleting selected folders: ' +
                                        response.message,
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary",
                                    }
                                });
                            }
                        },
                        error: function (xhr) {
                            Swal.fire({
                                text: "There was an error deleting folders. Please try again.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            });
                        }
                    });
                }

                // Handle document deletion
                if (selectedDocuments.length > 0) {
                    $.ajax({
                        url: '/admin/file/delete-selected',
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr(
                                'content'),
                            ids: selectedDocuments,
                            type: 'document'
                        },
                        success: function (response) {
                            if (response.success) {
                                $('.file-manager-checkbox:checked[data-type="document"]')
                                    .closest('tr').remove();
                                Swal.fire({
                                    icon: "success",
                                    text: response.message,
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary",
                                    }
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    buttonsStyling: false,
                                    text: 'Error deleting selected folders: ' +
                                        response.message,
                                    confirmButtonText: 'OK',
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary",
                                    }
                                });
                            }
                        },
                        error: function (xhr) {
                            Swal.fire({
                                text: "There was an error deleting files. Please try again.",
                                icon: "error",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
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
        const parent = e.target.closest('tr');
        const folderName = parent.querySelectorAll('td')[1].innerText;

        // SweetAlert confirmation dialog
        Swal.fire({
            text: "Are you sure you want to delete " + folderName + "?",
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: "Yes, delete!",
            cancelButtonText: "No, cancel",
            customClass: {
                confirmButton: "btn fw-bold btn-danger",
                cancelButton: "btn fw-bold btn-active-light-primary"
            }
        }).then((result) => {
            if (result.value) {

                // Send the delete request to the server
                $.ajax({
                    url: `/admin/folder-destroy/` +
                        folderId, // Assuming RESTful API convention
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        // Show success message
                        Swal.fire({
                            text: "You have deleted  " + folderName +
                                "!",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        }).then(function () {
                            // Delete row data from the table and re-draw datatable
                            row.remove();
                            toastr.options = {
                                "closeButton": false,
                                "debug": false,
                                "newestOnTop": false,
                                "progressBar": true,
                                "positionClass": "toastr-top-right",
                                "preventDuplicates": false,
                                "onclick": null,
                                "showDuration": "300",
                                "hideDuration": "1000",
                                "timeOut": "5000",
                                "extendedTimeOut": "1000",
                                "showEasing": "swing",
                                "hideEasing": "linear",
                                "showMethod": "fadeIn",
                                "hideMethod": "fadeOut"
                            };
                            toastr.success(response.message);
                        });

                        // return console.log(response);
                    },
                    error: function (xhr) {
                        // Handle the error
                        Swal.fire({
                            text: "There was an error deleting  " +
                                folderName + ". Please try again.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                    }
                });
            } else if (result.dismiss === 'cancel') {
                Swal.fire({
                    text: folderName + " was not deleted .",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary",
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
        const parent = e.target.closest('tr');
        const fileName = parent.querySelectorAll('td')[1].innerText;

        // SweetAlert confirmation dialog
        Swal.fire({
            text: "Are you sure you want to delete " + fileName + "?",
            icon: "warning",
            showCancelButton: true,
            buttonsStyling: false,
            confirmButtonText: "Yes, delete!",
            cancelButtonText: "No, cancel",
            customClass: {
                confirmButton: "btn fw-bold btn-danger",
                cancelButton: "btn fw-bold btn-active-light-primary"
            }
        }).then((result) => {

            if (result.value) {

                // Send the delete request to the server
                $.ajax({
                    url: `/admin/file-destroy/` +
                        documentId, // Assuming RESTful API convention
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        // Show success message
                        Swal.fire({
                            text: "You have deleted  " + fileName +
                                "!",
                            icon: "success",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        }).then(function () {
                            // Delete row data from the table and re-draw datatable
                            row.remove();
                            toastr.options = {
                                "closeButton": false,
                                "debug": false,
                                "newestOnTop": false,
                                "progressBar": true,
                                "positionClass": "toastr-top-right",
                                "preventDuplicates": false,
                                "onclick": null,
                                "showDuration": "300",
                                "hideDuration": "1000",
                                "timeOut": "5000",
                                "extendedTimeOut": "1000",
                                "showEasing": "swing",
                                "hideEasing": "linear",
                                "showMethod": "fadeIn",
                                "hideMethod": "fadeOut"
                            };
                            toastr.success(response.message);
                        });

                        // return console.log(response);
                    },
                    error: function (xhr) {
                        // Handle the error
                        Swal.fire({
                            text: "There was an error deleting  " +
                                fileName + ". Please try again.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                    }
                });
            } else if (result.dismiss === 'cancel') {
                Swal.fire({
                    text: fileName + " was not deleted .",
                    icon: "error",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary",
                    }
                });
            }
        });
    });

});
