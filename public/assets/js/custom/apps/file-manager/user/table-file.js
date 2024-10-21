"use strict";

// Class definition
var KTDatatablesServerSide = function () {
    // Shared variables
    var table;
    var dt;
    var filterPayment;

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
            ajax: {
                url: "/file-manager",
            },
            columns: [
                {
                    data: 'item_name',
                },
                {
                    data: 'org_name'
                },
                {
                    data: 'full_name'
                },
                {
                    data: null,
                },
                {
                    data: null
                },
            ],
            language: {
                emptyTable: `<div class="d-flex flex-column flex-center">\n
                    <img src="/assets/media/illustrations/sketchy-1/5.png" class="mw-400px" />\n
                    <div class="fs-1 fw-bolder text-dark mb-4">No items found.</div>\n
                 </div>`,

            },
            columnDefs: [{
                    targets: 0,
                    orderable: true,
                    width: '300px',

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
                            `/file-manager/${row.uuid}` // Folder link
                            :
                            `/file-details/${row.latest_version_guid}`; // Document link

                        // Return the rendered HTML
                        return `
                            <div class="d-flex align-items-center">
                                <span class="icon-wrapper">${iconHtml}</span>
                                <a href="${linkUrl}" class="text-gray-800 text-hover-primary">${data}</a>
                            </div>
                        `;
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
                               data-kt-docs-table-filter="starred_row"></i>`;
                    }
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
                            `/file-manager/${row.uuid}` // Folder link
                            :
                            `/file-details/${row.latest_version_guid}`; // Document link

                        // Return the rendered HTML

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

        dt.on('draw', function () {
            let itemCount = dt.rows({
                search: 'applied'
            }).count();
            $('#kt_file_manager_items_counter').text(`${itemCount} items`);

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
                url: `/star`, // Adjust the route as per your backend
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


    // Public methods
    return {
        init: function () {
            initDatatable();
            handleSearchDatatable();
            toggleStarred();
        }
    }
}();

// On document ready
KTUtil.onDOMContentLoaded(function () {
    KTDatatablesServerSide.init();
});
