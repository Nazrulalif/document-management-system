"use strict";

// Class definition
var KTDatatablesServerSide = function () {
    // Shared variables
    var table;
    var dt;
    var filterPayment;
    var uuid = $('#uuid').val();
    console.log(uuid);

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
                url: "/admin/company-detail/" + uuid,
            },
            columns: [{
                    data: null,
                    render: function (data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart +
                            1; // Column for displaying row number
                    },
                    orderable: true,
                },
                {
                    data: 'full_name',
                    render: function (data, type, row) {
                        return '<a class="text-gray-800 text-hover-primary" href="/admin/user-detail/' + row.uuid + '">' + data + '</a>';
                    },
                },
                {
                    data: 'email'
                },
                {
                    data: 'role_name'
                },
            ],
            columnDefs: [{
                data: 'org_name',
                targets: 1,
                className: 'text-gray-800',

            }, ],
            // Add data-filter attribute
            createdRow: function (row, data, dataIndex) {
                $(row).find('td:eq(4)').attr('data-filter', data.CreditCardType);
            }
        });

        table = dt.$;

        // Re-init functions on every table re-draw -- more info: https://datatables.net/reference/event/draw
        dt.on('draw', function () {
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


    // Public methods
    return {
        init: function () {
            initDatatable();
            handleSearchDatatable();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTDatatablesServerSide.init();
});
