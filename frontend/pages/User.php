<?php
  $page = "User";
  $css = '';
  include("../layouts/dashboard_layout.php")
?>
<main class="main-body p-3 d-flex flex-column">
  <div class="main-container p-5">
  <div class="d-flex justify-content-between align-items-center mb-5 top-head">
    <h2>Manage User</h2>
    <div>
      <button id="reset" class="btn btn-danger">Reset</button>
      <button id="addUser" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserInput">Add
        User</button>
    </div>
  </div>
  <div class="user-container table-container">
    <table id="UserTable" class="table table-bordered table-striped w-100">
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Phone no</th>
          <th>Email</th>
          <th>Address</th>
          <th>City</th>
          <th>Passport no</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
      <tfoot>
      <tr>
          <th>#</th>
          <th>Name</th>
          <th>Phone no</th>
          <th>Email</th>
          <th>Address</th>
          <th>City</th>
          <th>Passport no</th>
          <th>Actions</th>
        </tr>
      </tfoot>
    </table>
    <div class="modal fade" id="addUserInput" tabindex="-1" aria-labelledby="inputModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="inputModalLabel">Enter User Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="userForm">
              <div class="mb-3">
                <label for="userName1" class="form-label">Name</label>
                <input type="text" class="form-control" id="userName1" placeholder="Enter User's name" required>
              </div>
              <div class="mb-3">
                <label for="userPhone1" class="form-label">Phone</label>
                <input type="tel" class="form-control" id="userPhone1" placeholder="Enter User's Phone no" required>
              </div>
              <div class="mb-3">
                <label for="userEmail1" class="form-label">Email</label>
                <input type="email" class="form-control" id="userEmail1" placeholder="Enter User's email" required>
              </div>
              <div class="mb-3">
                <label class="form-label" for="userAddress1">Address</label>
                <textarea placeholder="Enter User's Address" class="form-control" name="userAddress" id="userAddress1"
                  maxlength="80"></textarea>
              </div>
              <div class="mb-3">
                <label for="userCity1" class="form-label">City</label>
                <input type="text" class="form-control" id="userCity1" placeholder="Enter User's City">
              </div>
              <div class="mb-3">
                <label for="userPassport1" class="form-label">Passport Number</label>
                <input type="text" class="form-control" id="userPassport1" placeholder="Enter User's Passport Number">
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button id="close" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button id="addUserBTN" type="button" class="btn btn-primary">Add</button>
          </div>
        </div>
      </div>
    </div>
    <div class="modal fade" id="editUserInput" tabindex="-1" aria-labelledby="inputModalLabel" aria-hidden="true">
      <div class="modal-dialog  modal-dialog-scrollable">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="inputModalLabel">Edit User Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="userForm">
              <div class="mb-3">
                <label for="userName2" class="form-label">Name</label>
                <input type="text" class="form-control" id="userName2" placeholder="Enter User's name" required>
              </div>
              <div class="mb-3">
                <label for="userPhone2" class="form-label">Phone</label>
                <input type="tel" class="form-control" id="userPhone2" placeholder="Enter User's Phone no" required>
              </div>
              <div class="mb-3">
                <label for="userEmail2" class="form-label">Email</label>
                <input type="email" class="form-control" id="userEmail2" placeholder="Enter User's email" required>
              </div>
              <div class="mb-3">
                <label class="form-label" for="userAddress2">Address</label>
                <textarea placeholder="Enter User's Address" class="form-control" name="userAddress" id="userAddress2"
                  maxlength="80"></textarea>
              </div>
              <div class="mb-3">
                <label for="userCity2" class="form-label">City</label>
                <input type="text" class="form-control" id="userCity2" placeholder="Enter User's City">
              </div>
              <div class="mb-3">
                <label for="userPassport2" class="form-label">Passport Number</label>
                <input type="text" class="form-control" id="userPassport2" placeholder="Enter User's Passport Number">
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button id="close" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button id="saveChangesBTN" type="button" class="btn btn-primary">Save Changes</button>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
</main>
<?php
 $script = "<script type='module' src='../assets/js/user.js'></script>";
include("../components/footer.php");
?>