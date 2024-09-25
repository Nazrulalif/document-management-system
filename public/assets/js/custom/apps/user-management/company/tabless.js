"use strict";

// Class definition
var KTDatatablesServerSide = function () {
    // Shared variables
    var table;
    var dt;
    var filterPayment;

    // Private functions
    var initDatatable = function () {
        dt = $("#kt_datatable_example_1").DataTable({
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
                url: "/admin/company-list",
            },
            columns: [{
                    data: 'id'
                },
                {
                    data: 'org_name'
                },
                {
                    data: 'org_place'
                },
                {
                    data: 'org_number'
                },
                {
                    data: 'nature_of_business'
                },
                {
                    data: 'formatted_date'
                },
                {
                    data: null
                },
            ],
            columnDefs: [{
                    targets: 0,
                    orderable: false,
                    render: function (data) {
                        return `
                            <div class="form-check form-check-sm form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="${data}" />
                            </div>`;
                    }
                },
                {
                    data: 'org_name',
                    targets: 1,
                    className: 'text-gray-800',

                },
                {
                    targets: -1,
                    data: null,
                    orderable: false,
                    className: 'text-end',
                    render: function (data, type, row) {
                        return `
                            <a href="#" class="btn btn-light btn-active-light-primary btn-sm" data-kt-menu-trigger="click" data-kt-menu-placement="bottom-end" data-kt-menu-flip="top-end">
                                Actions
                                <span class="svg-icon fs-5 m-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="24px" height="24px" viewBox="0 0 24 24" version="1.1">
                                        <g stroke="none" stroke-width="1" fill="none" fill-rule="evenodd">
                                            <polygon points="0 0 24 0 24 24 0 24"></polygon>
                                            <path d="M6.70710678,15.7071068 C6.31658249,16.0976311 5.68341751,16.0976311 5.29289322,15.7071068 C4.90236893,15.3165825 4.90236893,14.6834175 5.29289322,14.2928932 L11.2928932,8.29289322 C11.6714722,7.91431428 12.2810586,7.90106866 12.6757246,8.26284586 L18.6757246,13.7628459 C19.0828436,14.1360383 19.1103465,14.7686056 18.7371541,15.1757246 C18.3639617,15.5828436 17.7313944,15.6103465 17.3242754,15.2371541 L12.0300757,10.3841378 L6.70710678,15.7071068 Z" fill="currentColor" fill-rule="nonzero" transform="translate(12.000003, 11.999999) rotate(-180.000000) translate(-12.000003, -11.999999)"></path>
                                        </g>
                                    </svg>
                                </span>
                            </a>
                            <!--begin::Menu-->
                            <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-600 menu-state-bg-light-primary fw-bold fs-7 w-125px py-4" data-kt-menu="true">
                            <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <a href="/admin/company-detail/${data.uuid}" class="menu-link px-3" data-kt-docs-table-filter="view" data-id="${data.id}">
                                        View
                                    </a>
                                </div>
                                <!--end::Menu item-->

                            <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3" data-kt-docs-table-filter="edit_row" data-id="${data.id}">
                                        Edit
                                    </a>
                                </div>
                                <!--end::Menu item-->

                                <!--begin::Menu item-->
                                <div class="menu-item px-3">
                                    <a href="#" class="menu-link px-3" data-kt-docs-table-filter="delete_row" data-id="${data.id}">
                                        Deactive
                                    </a>
                                </div>
                                <!--end::Menu item-->
                            </div>
                            <!--end::Menu-->
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

        // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
        dt.on('draw', function () {
            initToggleToolbar();
            toggleToolbars();
            handleEditRows()
            handleDeleteRows();
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

                // Make an AJAX request to get data for the specific id
                $.ajax({
                    url: `/admin/company-show/${rowId}`, // URL to your endpoint
                    method: 'GET',
                    success: function (response) {
                        // Check if there was an error
                        if (response.error) {
                            console.error('Error:', response.error);
                            return;
                        }

                        // Assuming response.data is the company data
                        const rowData = response.data;

                        // Populate the form with row data
                        document.getElementById('editId').value = rowData.id;
                        document.getElementById('org_name').value = rowData.org_name;
                        document.getElementById('reg_date').value = rowData.reg_date;
                        document.getElementById('org_place').value = rowData.org_place;
                        document.getElementById('org_number').value = rowData.org_number;
                        document.getElementById('org_address').value = rowData.org_address;
                        document.getElementById('nature_of_business').value = rowData.nature_of_business;
                        // Add other fields as necessary

                        // Show the modal
                        new bootstrap.Modal(document.getElementById('kt_modal_edit_company')).show();
                    },
                    error: function (xhr) {
                        console.error('Failed to fetch data:', xhr.responseText);
                        Swal.fire({
                            text: "There was an error retrieving the company details. Please try again.",
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
    }

    var handleEditFormSubmission = () => {
        // Get the form element
        const editForm = document.getElementById('kt_modal_edit_company_form');

        if (!editForm) {
            console.error('Edit form not found!');
            return;
        }

        // Add submit event listener to the form
        editForm.addEventListener('submit', function (e) {
            e.preventDefault(); // Prevent the default form submission

            const formData = new FormData(this);
            const id = formData.get('id'); // Make sure your form has an input with name="id"

            // Perform AJAX request
            $.ajax({
                url: `/admin/company-update/${id}`, // URL for updating company details
                method: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // CSRF token
                },
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    // Show success message
                    Swal.fire({
                        text: "Company details updated successfully!",
                        icon: "success",
                        buttonsStyling: false,
                        confirmButtonText: "Ok, got it!",
                        customClass: {
                            confirmButton: "btn fw-bold btn-primary",
                        }
                    }).then(function () {
                        // Refresh the data table (if needed)
                        dt.draw(); // If you have a DataTable

                        // Hide the modal
                        const editModal = document.getElementById('kt_modal_edit_company');
                        if (editModal) {
                            const modalInstance = bootstrap.Modal.getInstance(editModal);
                            if (modalInstance) {
                                modalInstance.hide();
                            }
                        }
                    });
                },
                error: function (xhr, error) {
                    console.log(xhr);
                    Swal.fire({
                        text: "There was an error updating the company details. Please try again.",
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
    }

    // Delete organization by row 
    var handleDeleteRows = () => {

        // Select all delete buttons
        const deleteButtons = document.querySelectorAll('[data-kt-docs-table-filter="delete_row"]');
        // console.log(deleteButtons);

        deleteButtons.forEach(d => {
            // Delete button on click
            d.addEventListener('click', function (e) {
                e.preventDefault();

                // Get row ID from data-id attribute
                var rowId = this.getAttribute('data-id');

                // Select parent row
                const parent = e.target.closest('tr');

                // Get customer name
                const companyName = parent.querySelectorAll('td')[1].innerText;

                // console.log(rowId);

                // SweetAlert2 pop up --- official docs reference: https://sweetalert2.github.io/
                Swal.fire({
                    text: "Are you sure you want to deactivate  " + companyName + "?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Yes, deactivate!",
                    cancelButtonText: "No, cancel",
                    customClass: {
                        confirmButton: "btn fw-bold btn-danger",
                        cancelButton: "btn fw-bold btn-active-light-primary"
                    }
                }).then(function (result) {
                    if (result.value) {

                        // Send the delete request to the server
                        $.ajax({
                            url: `/admin/company-destroy/${rowId}`, // Assuming RESTful API convention
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                // Show success message
                                Swal.fire({
                                    text: "You have deactivated " + companyName + "!",
                                    icon: "success",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, got it!",
                                    customClass: {
                                        confirmButton: "btn fw-bold btn-primary",
                                    }
                                }).then(function () {
                                    // Delete row data from the table and re-draw datatable
                                    dt.draw();
                                });

                                // return console.log(response);
                            },
                            error: function (xhr) {
                                // Handle the error
                                Swal.fire({
                                    text: "There was an error deactivate  " + companyName + ". Please try again.",
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
                            text: companyName + " was not deactivate.",
                            icon: "error",
                            buttonsStyling: false,
                            confirmButtonText: "Ok, got it!",
                            customClass: {
                                confirmButton: "btn fw-bold btn-primary",
                            }
                        });
                    }
                });
            })
        });
    }

    // Init toggle toolbar
    var initToggleToolbar = function () {
        const container = document.querySelector('#kt_datatable_example_1');
        const checkboxes = container.querySelectorAll('[type="checkbox"]');
        const deleteSelected = document.querySelector('[data-kt-docs-table-select="delete_selected"]');

        // Toggle delete selected toolbar
        checkboxes.forEach(c => {
            c.addEventListener('click', function () {
                setTimeout(function () {
                    toggleToolbars();
                }, 50);
            });
        });

        // Delete selected rows
        deleteSelected.addEventListener('click', function () {
            // Gather selected row IDs
            const selectedIds = [];
            checkboxes.forEach(checkbox => {
                if (checkbox.checked && checkbox !== container.querySelector('[type="checkbox"]')) { // Skip header checkbox
                    selectedIds.push(checkbox.value);
                }
            });

            if (selectedIds.length === 0) {
                Swal.fire({
                    text: "No companies selected.",
                    icon: "info",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary",
                    }
                });
                return;
            }

            // SweetAlert2 pop up
            Swal.fire({
                text: "Are you sure you want to deactivate selected companies?",
                icon: "warning",
                showCancelButton: true,
                buttonsStyling: false,
                showLoaderOnConfirm: true,
                confirmButtonText: "Yes, deactivate!",
                cancelButtonText: "No, cancel",
                customClass: {
                    confirmButton: "btn fw-bold btn-danger",
                    cancelButton: "btn fw-bold btn-active-light-primary"
                }
            }).then(function (result) {
                if (result.value) {
                    // Send the delete request to the server
                    $.ajax({
                        url: '/admin/company-bulk-destroy', // Modify URL as needed
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        method: 'POST',
                        data: {
                            ids: selectedIds
                        },
                        success: function (response) {
                            // console.log(response);
                            Swal.fire({
                                text: "You have deactivate all selected companies!",
                                icon: "success",
                                buttonsStyling: false,
                                confirmButtonText: "Ok, got it!",
                                customClass: {
                                    confirmButton: "btn fw-bold btn-primary",
                                }
                            }).then(function () {
                                dt.draw();
                            });

                            // Remove header checked box
                            const headerCheckbox = container.querySelectorAll('[type="checkbox"]')[0];
                            headerCheckbox.checked = false;
                        },
                        error: function () {
                            Swal.fire({
                                text: "There was an error deactivate selected companies. Please try again.",
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
                        text: "Selected companies were not deactivate.",
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
    }


    // Toggle toolbars
    var toggleToolbars = function () {
        // Define variables
        const container = document.querySelector('#kt_datatable_example_1');
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
            handleDeleteRows();
            handleEditFormSubmission();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTDatatablesServerSide.init();
});
