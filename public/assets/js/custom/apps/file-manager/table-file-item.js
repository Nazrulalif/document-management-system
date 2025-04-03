"use strict";

// Class definition
var KTDatatablesServerSide = function () {
    // Shared variables
    var table;
    var dt;
    var filterPayment;

    const uuidElement = document.getElementById('uuid-folder');
    const uuid = uuidElement.value;
    // Private functions
    var initDatatable = function () {
        dt = $("#kt_datatable_example_2").DataTable({
            searchDelay: 500,
            // processing: true,
            serverSide: true,
            order: [
                [5, 'desc']
            ],
            stateSave: true,
            select: {
                style: 'multi',
                selector: 'td:first-child input[type="checkbox"]',
                className: 'row-selected'
            },
            ajax: {
                url: `/admin/file-manager/${uuid}`,
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'item_name'
                },
                {
                    data: 'shared_orgs'
                },
                {
                    data: 'full_name'
                },
                {
                    data: null
                },
                {
                    data: null
                },
            ],
            language: {
                emptyTable: `<div class="d-flex flex-column flex-center">\n
                    <img src="/assets/media/illustrations/sketchy-1/5.png" class="mw-400px" />\n
                    <div class="fs-1 fw-bolder text-dark mb-4">No items found.</div>\n
                    <div class="fs-6">Start creating new folders or uploading a new file!</div>\n
                 </div>`,

            },
            columnDefs: [{
                    targets: 1,
                    orderable: false,
                    render: function (data, type, row) {
                        let iconHtml;

                        // Determine the icon based on the document type
                        switch (row.doc_type) {
                            case 'pdf':
                                iconHtml = `<img src="/assets/media/icons/duotune/files/pdf-file.png" class="mw-30px me-4" alt="PDF Icon">`;
                                break;
                            case 'doc':
                            case 'docx':
                                iconHtml = `<img src="/assets/media/icons/duotune/files/word-file.png" 
                                             class="mw-30px me-4" alt="Word Icon" />`;
                                break;
                            case 'xlsx':
                            case 'csv':
                                iconHtml = `<img src="/assets/media/icons/duotune/files/excel-file.png" 
                                             class="mw-30px me-4" alt="Excel Icon" />`;
                                break;
                            case 'pptx':
                                iconHtml = `<img src="/assets/media/icons/duotune/files/pptx-file.png" 
                                             class="mw-30px me-4" alt="PowerPoint Icon" />`;
                                break;
                            case 'images':
                                iconHtml = `<img src="/assets/media/icons/duotune/files/image-file.png" 
                                             class="mw-30px me-4" alt="Image Icon" />`;
                                break;
                            default:
                                // Default folder icon for unknown types
                                iconHtml = `
                                    <i class="ki-duotone ki-folder fs-2x text-primary me-4">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i>`;
                        }

                        // Combine icon with a clickable name link
                        // Set appropriate link based on item type (folder or document)
                        let linkUrl = row.doc_type === null ?
                            `/admin/file-manager/${row.uuid}` // Folder link
                            :
                            `/admin/file-details/${row.latest_version_guid}`; // Document link

                        // Return the rendered HTML
                        return `
                            <div class="d-flex align-items-center">
                                <span class="icon-wrapper">${iconHtml}</span>
                                <a href="${linkUrl}" class="text-gray-800 text-hover-primary">${data}</a>
                            </div>
                        `;
                    }
                }, {
                    targets: 0,
                    orderable: false,
                    render: function (data, row) {
                        return `
                              <div class="form-check form-check-sm form-check-custom form-check-solid">
                            <input class="form-check-input" type="checkbox" value="${data}" data-type="${row.doc_type === null ? 'folder' : 'document'}" />
                        </div>`;
                    }
                },
                {
                    targets: -2, // Adjust to the appropriate column index
                    orderable: false,
                    className: 'text-center',
                    render: function (data, type, row) {
                        // Determine whether the document is starred or not
                        const isStarred = row.is_starred; // Backend should provide this field
                        const iconClass = isStarred ? 'ki-duotone text-warning' : 'ki-outline';
                        return `
                            <i class="${iconClass} ki-star fs-3 star-icon cursor-pointer" 
                               data-id="${row.id}" data-type="${row.doc_type === null ? 'folder' : 'document'}"  
                               data-kt-docs-table-filter="starred_row"></i>` ;
                    }
                },
                {
                    targets: 1,
                    orderable: false,
                    width: '300px'  
                },
                {
                    targets: -1,
                    data: null,
                    orderable: false,
                    className: 'text-end',
                    render: function (data, type, row) {
                        // Combine icon with a clickable name link
                        // Set appropriate link based on item type (folder or document)
                        let linkUrl = row.doc_type === null ?
                            `/admin/file-manager/${row.uuid}` // Folder link
                            :
                            `/admin/file-details/${row.latest_version_guid}`; // Document link

                            // let renameMenuItem = row.doc_type === null
                            // ? `<div class="menu-item px-3">
                            //       <a class="menu-link px-3" data-kt-docs-table-filter="edit_row" data-id="${data.id}">Rename</a>
                            //     </div>`
                            // : '';

                        return `
                            <td class="text-end">
                                        <div class="d-flex justify-content-end">
                                            <button type="button"
                                                class="btn btn-sm btn-icon btn-light btn-active-light-primary me-2"
                                                data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end">
                                                <i class="ki-duotone ki-dots-square fs-5 m-0">
                                                    <span class="path1"></span>
                                                    <span class="path2"></span>
                                                    <span class="path3"></span>
                                                    <span class="path4"></span>
                                                </i>
                                            </button>
                                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-semibold fs-7 w-150px py-4"
                                                data-kt-menu="true">
                                                <div class="menu-item px-3">
                                                    <a href="${linkUrl}"
                                                        class="menu-link px-3">View</a>
                                                </div>
                                                  <div class="menu-item px-3">
                                                    <a class="menu-link px-3" data-type="${row.doc_type === null ? 'folder' : 'document'}" data-kt-docs-table-filter="edit_row" data-id="${data.id}" data-share-company="${row.shared_orgs_guid}">Edit</a>
                                                </div>
                                                <div class="menu-item px-3">
                                                    <a href="#" class="menu-link text-danger px-3" data-kt-docs-table-filter="delete_row" data-id="${data.id}"
                                                    data-type="${row.doc_type === null ? 'folder' : 'document'}">Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                        `;
                    },
                },
            ],

            // Add data-filter attribute
            createdRow: function (row, data, dataIndex) {
                $(row).find('td:eq(4)').attr('data-filter', data.CreditCardType);
            }
        });

        table = dt.$;
        
        dt.search('').draw();
        dt.column(2).search('').draw(); 

        dt.on('draw', function () {
            let itemCount = dt.rows({
                search: 'applied'
            }).count();
            $('#kt_file_manager_items_counter').text(`${itemCount} items`);

            initToggleToolbar();
            toggleToolbars();
            handleEditRows()
            handleDeactiveRows();
            toggleStarred();

            KTMenu.createInstances();
        });
    }

    // Search Datatable --- official docs reference: https://datatables.net/reference/api/search()
    var handleSearchDatatable = function () {
        const filterSearch = document.querySelector('[data-kt-docs-table-filter="search"]');
        filterSearch.addEventListener('keyup', function (e) {
            dt.search(e.target.value).draw();
        });
    }

    var handleEditRows = () => {
        // Select all edit buttons
        const editButtons = document.querySelectorAll('[data-kt-docs-table-filter="edit_row"]');
    
        editButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();
                
                const rowId = this.getAttribute('data-id');
                const rowData = dt.row($(this).closest('tr')).data();
                const type = this.getAttribute('data-type');

                // Safely retrieve the organization ID (or default to empty)
                const rowShare = this.getAttribute('data-share-company') || '';
    
                if (type === 'folder') {
                    // Populate modal input with folder data
                    document.getElementById('edit_folder').value = rowData.item_name;
                    document.getElementById('folderId').value = rowId;
                    document.getElementById('org_select_edit').value = rowData.shared_orgs_guid;
    
                    // Show the modal
                    const editModal = new bootstrap.Modal(document.getElementById('kt_modal_edit_folder'));
                    editModal.show();
                } else {
                    // Populate modal input with file data
                    document.getElementById('edit_file').value = rowData.item_name;
                    document.getElementById('fileId').value = rowId;
                    document.getElementById('org_select_file_edit').value = rowData.shared_orgs_guid;

                    // Show the modal
                    const editModal = new bootstrap.Modal(document.getElementById('kt_modal_edit_file'));
                    editModal.show();
                }
            });
        });
    };

    var handleEditFormSubmission = () => {
        // Get the form element
        const editForm = document.getElementById('kt_modal_edit_folder_form');

        if (!editForm) {
            console.error('Edit form not found!');
            return;
        }

        // Add submit event listener to the form
        editForm.addEventListener('submit', function (e) {
            e.preventDefault(); // Prevent default form submission

            const formData = new FormData(this);
            const id = formData.get('folderId');
            const name = formData.get('edit_folder');
            const share_guids = formData.get('org_name_edit');

            // Perform AJAX request
            $.ajax({
                url: `/admin/folder-rename/${id}`, // Adjusted to match your route
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token
                },
                data: {
                    folder_name: name, // Data to be sent to the backend
                    org_name_edit: share_guids 
                },
                success: function (response) {
                    // Show success message
                    console.log(response.message);

                    Swal.fire({
                        text: response.message,
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    }).then(function () {
                        // Refresh DataTable if applicable
                        if (typeof dt !== 'undefined') dt.draw();

                        // Hide the modal
                        const editModal = document.getElementById('kt_modal_edit_folder');
                        if (editModal) {
                            const modalInstance = bootstrap.Modal.getInstance(editModal);
                            if (modalInstance) {
                                modalInstance.hide();
                            }
                        }
                    });
                },
                error: function (xhr, error) {
                    Swal.fire({
                        text: "There was an error updating the folder. Please try again.",
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
    };

    var handleEditFileFormSubmission = () => {
        // Get the form element
        const editForm = document.getElementById('kt_modal_edit_file_form');

        if (!editForm) {
            console.error('Edit form not found!');
            return;
        }

        // Add submit event listener to the form
        editForm.addEventListener('submit', function (e) {
            e.preventDefault(); // Prevent default form submission

            const formData = new FormData(this);
            const id = formData.get('fileId');
            const name = formData.get('edit_file');
            const share_guids = formData.get('org_name_file_edit');

            // Perform AJAX request
            $.ajax({
                url: `/admin/file-rename/${id}`, // Adjusted to match your route
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token
                },
                data: {
                    edit_file: name, // Data to be sent to the backend
                    org_name_edit: share_guids 
                },
                success: function (response) {
                    // Show success message
                    console.log(response.message);

                    Swal.fire({
                        text: response.message,
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    }).then(function () {
                        // Refresh DataTable if applicable
                        if (typeof dt !== 'undefined') dt.draw();

                        // Hide the modal
                        const editModal = document.getElementById('kt_modal_edit_file');
                        if (editModal) {
                            const modalInstance = bootstrap.Modal.getInstance(editModal);
                            if (modalInstance) {
                                modalInstance.hide();
                            }
                        }
                    });
                },
                error: function (xhr, error) {
                    Swal.fire({
                        text: "There was an error updating the folder. Please try again.",
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
    };

    // Delete organization by row 

    var handleDeactiveRows = () => {
        const deleteButtons = document.querySelectorAll('[data-kt-docs-table-filter="delete_row"]');

        deleteButtons.forEach(button => {
            button.addEventListener('click', function (e) {
                e.preventDefault();

                const rowId = this.getAttribute('data-id');
                const type = this.getAttribute('data-type'); // 'folder' or 'document'
                const parent = e.target.closest('tr');
                const itemName = parent.querySelectorAll('td')[1].innerText;

                // Set the appropriate delete URL based on the item type
                const deleteUrl = type === 'folder' ?
                    `/admin/folder-destroy/${rowId}` :
                    `/admin/file-destroy/${rowId}`;

                Swal.fire({
                    text: `Are you sure you want to delete ${itemName}?`,
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                }).then(result => {
                    if (result.value) {
                        // Send the delete request to the server
                        $.ajax({
                            url: deleteUrl,
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                Swal.fire({
                                    text: `${itemName} has been deleted!`,
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary",
                                    }
                                }).then(() => {
                                    dt.draw(); // Redraw the table
                                });
                            },
                            error: function (xhr, error) {
                                console.log(xhr);
                                console.log(error);
                                Swal.fire({
                                    text: `There was an error deleting ${itemName}. Please try again.`,
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
                            text: `${itemName} was not deleted.`,
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
    };


    // Init toggle toolbar
    var initToggleToolbar = function () {
        const container = document.querySelector('#kt_datatable_example_2');
        const checkboxes = container.querySelectorAll('[type="checkbox"]');
        const deleteSelected = document.querySelector('[data-kt-docs-table-select="delete_selected"]');

        checkboxes.forEach(c => {
            c.addEventListener('click', function () {
                setTimeout(toggleToolbars, 50);
            });
        });

        deleteSelected.addEventListener('click', function () {
            const selectedFolders = [];
            const selectedFiles = [];

            checkboxes.forEach(checkbox => {
                if (checkbox.checked && checkbox !== container.querySelector('[type="checkbox"]')) {
                    const row = dt.row($(checkbox).closest('tr')).data();
                    if (row.doc_type === null) {
                        selectedFolders.push(checkbox.value);
                    } else {
                        selectedFiles.push(checkbox.value);
                    }
                }
            });

            if (selectedFolders.length === 0 && selectedFiles.length === 0) {
                Swal.fire({
                    text: "No items selected.",
                    icon: "info",
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary"
                    }
                });
                return;
            }

            Swal.fire({
                text: "Are you sure you want to delete the selected items?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel",
                customClass: {
                    confirmButton: "btn fw-bold btn-danger",
                    cancelButton: "btn fw-bold btn-active-light-primary"
                }
            }).then(function (result) {
                if (result.value) {
                    if (selectedFolders.length > 0) {
                        sendDeleteRequest('/admin/folder/delete-selected', selectedFolders);
                    }
                    if (selectedFiles.length > 0) {
                        sendDeleteRequest('/admin/file/delete-selected', selectedFiles);
                    }
                }
            });
        });
    };

    var toggleStarred = function () {

        const starred = $('[data-kt-docs-table-filter="starred_row"]');

        // Attach a click event listener to each star icon
        starred.on('click', function (e) {
            e.preventDefault();

            const element = $(this); // Reference to the clicked element
            const id = element.data('id'); // Get the item ID
            const type = element.data('type'); // Get the item type (folder/document)

            // Send an AJAX request to toggle the star status
            $.ajax({
                url: `/admin/star`, // Adjust the route as per your backend
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    id: id,
                    type: type,
                },
                success: function (response) {
                    if (response.success) {
                        // Toggle the icon classes based on the new star status
                        if (response.starred) {

                            element.removeClass('ki-outline').addClass('ki-duotone text-warning');
                        } else {
                            element.removeClass('ki-duotone text-warning').addClass('ki-outline');
                        }
                    } else {
                        alert('Failed to update star status. Please try again.');
                    }
                },
                error: function () {
                    alert('Something went wrong. Please try again.');
                }
            });
        });
    };

    function sendDeleteRequest(url, ids) {
        $.ajax({
            url: url,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            method: 'POST',
            data: {
                ids: ids
            },
            success: function () {
                Swal.fire({
                    text: "Selected items deleted successfully!",
                    icon: "success",
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary"
                    }
                }).then(() => dt.draw());
            },
            error: function () {
                Swal.fire({
                    text: "Error deleting selected items. Please try again.",
                    icon: "error",
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary"
                    }
                });
            }
        });
    }


    // Toggle toolbars
    var toggleToolbars = function () {
        // Define variables
        const container = document.querySelector('#kt_datatable_example_2');
        const toolbarBase = document.querySelector('[data-kt-docs-table-toolbar="base"]');
        const toolbarSelected = document.querySelector('[data-kt-docs-table-toolbar="selected"]');
        const selectedCount = document.querySelector('[data-kt-docs-table-select="selected_count"]');

        // Select refreshed checkbox DOM elements
        const allCheckboxes = container.querySelectorAll('tbody [type="checkbox"]');

        // Detect checkboxes state & count
        let checkedState = false;
        let count = 0;

        // Count checked boxes
        allCheckboxes.forEach(c => {
            if (c.checked) {
                checkedState = true;
                count++;
            }
        });

        // Toggle toolbars
        if (checkedState) {
            selectedCount.innerHTML = count;
            toolbarBase.classList.add('d-none');
            toolbarSelected.classList.remove('d-none');
        } else {
            toolbarBase.classList.remove('d-none');
            toolbarSelected.classList.add('d-none');
        }
    }

    // Public methods
    return {
        init: function () {
            initDatatable();
            handleSearchDatatable();
            initToggleToolbar();
            handleEditRows();
            handleDeactiveRows();
            handleEditFormSubmission();
            toggleStarred();
            handleEditFileFormSubmission();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTDatatablesServerSide.init();
});
