<?php
  $page = "Attendance";
  $css = '';
  include("../layouts/dashboard_layout.php")
?>
<main class="main-body p-3 d-flex flex-column">
  <div class="main-container p-5">
  <div class="d-flex justify-content-between align-items-center mb-5 top-head">
    <h2>Mark Attendance</h2>
    <div>
      <button id="reset" class="btn btn-danger">Reset</button>
      <button id="addEmployee" class="ms-2 btn btn-primary" data-bs-toggle="modal"
        data-bs-target="#markAttendanceInput">Add Attendance</button>
    </div>
  </div>
  <div class="attendance-container table-container w-100">
    <table id="AttendanceTable" class="table table-bordered table-striped w-100">
      <thead>
        <tr>
          <th>#</th>
          <th>Name</th>
          <th>Date</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
      </tbody>
    </table>
  </div>

  <div class="modal fade" id="markAttendanceInput" tabindex="-1" aria-labelledby="inputModalLabel">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="inputModalLabel">Mark Employee Attendance</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="attendanceForm1">
            <div class="mb-3">
              <label for="employeeName1" class="form-label">Employee Name</label>
              <select class="form-control" id="employeeName1" name="EmployeeName">
                <option value="" selected>Select</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="employeeDate1" class="form-label">Select a Date</label>
              <input class="form-control date-picker" type="date" name="employeeDate" id="employeeDate1">
            </div>
            <div class="mb-3">
              <label for="employeeStatus1" class="form-label">Status</label>
              <select class="form-control" id="employeeStatus1">
                <option value="" selected>Select</option>
                <option value="present">Present</option>
                <option value="absent">Absent</option>
                <option value="leave">Leave</option>
              </select>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button id="close" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button id="addAttendanceBTN" type="button" class="btn btn-primary">Add</button>
        </div>
      </div>
    </div>
  </div>
  <div class="modal fade" id="editAttendanceInput" tabindex="-1" aria-labelledby="inputModalLabel">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="inputModalLabel">Update Employee Attendance</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="attendanceForm2">
            <div class="mb-3">
              <label for="employeeName2" class="form-label">Employee Name</label>
              <select class="form-control" id="employeeName2" name="EmployeeName">
                <option selected>Select</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="employeeDate2" class="form-label">Select a Date</label>
              <input class="form-control date-picker" type="date" name="employeeDate" id="employeeDate2">
            </div>
            <div class="mb-3">
              <label for="employeeStatus2" class="form-label">Status</label>
              <select class="form-control" id="employeeStatus2">
                <option>Select</option>
                <option value="present">Present</option>
                <option value="absent">Absent</option>
                <option value="leave">Leave</option>
              </select>
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
  <?php include('../components/forms.php')?>
  </div>
</main>
<?php
 $script = "<script type='module' src='../assets/js/attendance.js'></script>";
include("../components/footer.php");
?>