"use strict";

// Class definition
var KTDatatablesServerSide = function () {
    // Shared variables
    var table;
    var dt;
    var filterPayment;

    // Private functions
    var initDatatable = function () {
        dt = $("#kt_datatable_example_3").DataTable({
            searchDelay: 500,
            // processing: true,
            serverSide: true,
            order: [
                [5, 'desc']
            ],
            stateSave: true,
            ajax: {
                url: "/admin/deactivated-list",
            },
            columns: [
                {
                    data: 'full_name'
                },
                {
                    data: 'email'
                },
                {
                    data: 'role_name'
                },
                {
                    data: 'last_login_at'
                },
                {
                    data: null
                },
            ],
            columnDefs: [
                {
                    data: 'full_name',
                    targets: 0,
                    className: 'text-gray-800',

                },
                {
                    targets: -1,
                    data: null,
                    orderable: false,
                    className: 'text-end',
                    render: function (data, type, row) {
                        return `
                            <button class="btn btn-success btn-sm" data-id="${data.id}" data-kt-docs-table-filter="activate_row">
                                Activate
                            </button>
                            
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
            handleActivateRow();
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

     // activate user by row
     var handleActivateRow = () => {

        // Select all delete buttons
        const activateButton = document.querySelectorAll('[data-kt-docs-table-filter="activate_row"]');

        activateButton.forEach(d => {
            // Delete button on click
            d.addEventListener('click', function (e) {
                e.preventDefault();

                // Get row ID from data-id attribute
                var rowId = this.getAttribute('data-id');

                // Select parent row
                const parent = e.target.closest('tr');

                // Get customer name
                const userName = parent.querySelectorAll('td')[0].innerText;

                // SweetAlert2 pop up --- official docs reference: https://sweetalert2.github.io/
                Swal.fire({
                    text: "Are you sure you want to activated " + userName + "?",
                    icon: "warning",
                    showCancelButton: true,
                    buttonsStyling: false,
                    confirmButtonText: "Yes, Activate!",
                    cancelButtonText: "No, cancel",
                    customClass: {
                        confirmButton: "btn fw-bold btn-success",
                        cancelButton: "btn fw-bold btn-active-light-primary",
                    }
                }).then(function (result) {
                    if (result.value) {

                        // Send the delete request to the server
                        $.ajax({
                            url: `/admin/user-activate/${rowId}`, // Assuming RESTful API convention
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function (response) {
                                // Show success message
                                Swal.fire({
                                    text: "You have activated  " + response.message + "!",
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
                                    text: "There was an error activated  " + userName + ". Please try again.",
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
                            text: userName + " was not activated.",
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

    // Public methods
    return {
        init: function () {
            initDatatable();
            handleSearchDatatable();
            handleActivateRow();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTDatatablesServerSide.init();
});
