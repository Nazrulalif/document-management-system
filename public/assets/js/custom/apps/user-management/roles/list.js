document.addEventListener('DOMContentLoaded', function () {
    // Select all edit buttons
    const editButtons = document.querySelectorAll('[data-kt-docs-table-filter="edit_row"]');

    editButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();

            const rowId = this.getAttribute('data-id');

            // Make an AJAX request to get data for the specific role
            $.ajax({
                url: `/admin/role-show/${rowId}`, // URL to your endpoint
                method: 'GET',
                success: function (response) {
                    // Check if there was an error in the response
                    if (response.error) {
                        console.error('Error:', response.error);
                        return;
                    }

                    const rowData = response.data;

                    // Populate the modal form with the data
                    document.getElementById('role_id').value = rowData.id;
                    document.getElementById('role_name').value = rowData
                        .role_name;
                    document.getElementById('role_description').value = rowData
                        .role_description;
                    // Add other fields as necessary for your form
                },
                error: function (xhr) {
                    console.error('Failed to fetch data:', xhr.responseText);
                    Swal.fire({
                        text: "There was an error retrieving the role details. Please try again.",
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
});

document.addEventListener('DOMContentLoaded', function () {
    const editForm = document.getElementById('kt_modal_update_role_form');

    if (!editForm) {
        console.error('Edit form not found!');
        return;
    }

    // Add submit event listener to the form
    editForm.addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent the default form submission

        const formData = new FormData(this);
        const id = formData.get('role_id'); // Get the role ID

        // Perform AJAX request
        $.ajax({
            url: `/admin/role-update/${id}`, // URL for updating role details
            method: 'POST', // Ensure this matches your route's method
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                    'content') // CSRF token for security
            },
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                // Show success message using SweetAlert
                Swal.fire({
                    text: "Role details updated successfully!",
                    icon: "success",
                    buttonsStyling: false,
                    confirmButtonText: "Ok, got it!",
                    customClass: {
                        confirmButton: "btn fw-bold btn-primary",
                    }
                }).then(function () {
                    // Hide the modal
                    const editModal = document.getElementById(
                        'kt_modal_update_role');
                    if (editModal) {
                        const modalInstance = bootstrap.Modal.getInstance(
                            editModal);
                        if (modalInstance) {
                            modalInstance.hide();
                            window.location.reload();
                        }
                    }
                });
            },
            error: function (xhr, error) {
                console.log(xhr);
                Swal.fire({
                    text: "There was an error updating the role details. Please try again.",
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