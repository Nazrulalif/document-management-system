<div class="modal fade" tabindex="-1" id="kt_modal_1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Change Your Password</h3>

                <!--begin::Close-->
                <div class="btn btn-icon btn-sm btn-active-light-primary ms-2" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ki-duotone ki-cross fs-1"><span class="path1"></span><span class="path2"></span></i>
                </div>
                <!--end::Close-->
            </div>

            <div class="modal-body">
                <p><strong>Welcome!</strong></p>
                <p>For security purposes, please set a new password before you continue.</p>
                
                <p><strong>Instructions:</strong></p>
                <p>Enter a new password that meets the following requirements:</p>
                
                <ul>
                    <li>Minimum of 8 characters</li>
                    <li>At least one uppercase letter</li>
                    <li>At least one number</li>
                    <li>At least one special character (e.g., !, @, #, $)</li>
                </ul>
                
                <p>Confirm your new password by entering it again.</p>
                
                <p>Once updated, you’ll be able to access your account with your new credentials.</p>
                
                <p>Thank you for helping us keep your account secure!</p>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                <a href="{{ route('profile.setting') }}#kt_change_password" class="btn btn-primary">Change Password</a>
            </div>
        </div>
    </div>
</div>