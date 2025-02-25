<div class="modal fade" id="addAdmin" tabindex="-1" aria-labelledby="inputModalLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inputModalLabel">Add New Admin</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addAdminForm">
                    <div class="mb-3">
                        <label for="adminName" class="form-label">Admin Name</label>
                        <input type="text" class="form-control" id="adminName" placeholder="Enter Admin Name"
                            required>
                    </div>
                    <div class="mb-3">
                        <label for="adminEmail" class="form-label">Admin Email</label>
                       <input type="text" class="form-control" id="adminEmail" placeholder="Enter Admin Email">
                    </div>
                    <div class="mb-3">
                        <label for="adminPhone" class="form-label">Admin Phone Number</label>
                       <input type="text" class="form-control" id="adminPhone" placeholder="Enter Admin Phone Number">
                    </div>
                    <div class="mb-3">
                        <label for="adminPassword" class="form-label">Admin Password</label>
                       <input type="password" class="form-control" id="adminPassword" placeholder="Enter Admin Password" autocomplete="on">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="close" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button id="addAdminBTN" type="button" class="btn btn-primary">Add Admin</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="changePassword" tabindex="-1" aria-labelledby="inputModalLabel">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inputModalLabel">Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="changePasswordForm">
                    <div class="mb-3">
                        <label for="oldPassword" class="form-label">Old Password</label>
                        <input type="password" name="old-password" class="form-control" id="oldPassword" placeholder="Enter the old Password" autocomplete="on" required>
                    </div>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password</label>
                        <input type="password" class="form-control"  name="new-password" id="newPassword" placeholder="Enter the new Password" autocomplete="on" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirmPassword" class="form-label">Re-Enter New Password</label>
                        <input type="password" class="form-control"  name="confirm-password" id="confirmPassword" autocomplete="on" required placeholder="Re-Enter the new Password">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button id="close" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button id="changePasswordBTN" type="button" class="btn btn-primary">Change Password</button>
            </div>
        </div>
    </div>
</div>