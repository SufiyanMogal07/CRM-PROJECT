<?php
$page = "Employee";
$css = '<link rel="stylesheet" href="../assets/css/employee.css">';
include("../layouts/dashboard_layout.php")
?>
<main class="main-body d-flex flex-column p-3">
  <div class="main-container p-5">
    <div class="d-flex justify-content-between align-items-center mb-5 top-head">
      <h2>Manage Employee</h2>
      <div>
        <button id="reset" class="btn btn-danger">Reset</button>
        <button id="addEmployee" class="ms-2 btn btn-primary" data-bs-toggle="modal"
          data-bs-target="#addEmployeeInput">Add Employee</button>
      </div>
    </div>
    <div class="employee-container table-container">
      <table id="EmployeeTable" class="table table-bordered table-striped w-100">
        <thead>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
        <tfoot>
          <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Actions</th>
          </tr>
        </tfoot>
      </table>
      <!-- Add Employee Modal -->
    </div>

    <!-- Add Employee Modal -->
    <div class="modal fade" id="addEmployeeInput" tabindex="-1" aria-labelledby="inputModalLabel">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="inputModalLabel">Enter Employee Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="userForm1">
              <div class="mb-3">
                <label for="employeeName1" class="form-label">Name</label>
                <input type="text" class="form-control" id="employeeName1" placeholder="Enter Employee name"
                  required>
              </div>
              <div class="mb-3">
                <label for="employeeEmail1" class="form-label">Email</label>
                <input type="email" class="form-control" id="employeeEmail1" placeholder="Enter Employee email"
                  required autocomplete="email">
              </div>
              <div class="mb-3">
                <label for="employeePhone1" class="form-label">Phone</label>
                <input type="tel" class="form-control" id="employeePhone1" placeholder="Enter Employee Phone no"
                  required>
              </div>
              <div class="mb-3">
                <label for="employeePassword1" class="form-label">Password</label>
                <input type="password" class="form-control" id="employeePassword1"
                  placeholder="Enter Employee Password" required autocomplete="current-password">
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button id="close" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button id="addEmployeeBTN" type="button" class="btn btn-primary">Add</button>
          </div>
        </div>
      </div>
    </div>

    <!-- Edit Employee Modal -->
    <div class="modal fade" id="editEmployeeInput" tabindex="-1" aria-labelledby="inputModalLabel"
      >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="inputModalLabel">Edit Employee Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="userForm2">
              <div class="mb-3">
                <label for="employeeName2" class="form-label">Name</label>
                <input type="text" class="form-control" id="employeeName2" placeholder="Employee name" required>
              </div>
              <div class="mb-3">
                <label for="employeeEmail2" class="form-label">Email</label>
                <input type="email" class="form-control" id="employeeEmail2" placeholder="Employee email"
                  required>
              </div>
              <div class="mb-3">
                <label for="employeePhone2" class="form-label">Phone</label>
                <input type="tel" class="form-control" id="employeePhone2" placeholder="Employee Phone no"
                  required>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button id="close" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button data-id="1" id="saveChangesBTN" type="button" class="btn btn-primary">Save Changes</button>
          </div>
        </div>
      </div>

    </div>
    <?php include('../components/forms.php')?>
    <p class="pt-3 text-center fw-semibold">CRM PROJECT&copy; 2025</p>
  </div>
</main>
<?php
$script = "<script type='module' src='../assets/js/employee.js'></script>";
include("../components/footer.php");
?>