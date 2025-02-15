<?php
$page = "Call Logs";
$css = '';
include("../layouts/dashboard_layout.php")
?>
<main class="main-body d-flex flex-column p-3">
  <div class="main-container p-5">
  <div class="d-flex justify-content-between align-items-center mb-5 top-head">
    <h2 class="admin-section">Manage Employee Call Logs</h2>
    <h2 class="employee-section">Manage Your Call Logs</h2>
    <div>
      <button id="reset" class="btn btn-danger">Reset</button>
      <button class="btn btn-primary" data-bs-toggle="modal"  id="addCallLogs" data-bs-target="#addCallLogsInput">Add CallLogs</button>
    </div>
  </div>
  <div class="callLogs-container table-container">
    <table id="CallLogsTable" class="table table-bordered table-striped w-100">
      <thead>
        <tr>
          <th>#</th>
          <th class="admin-section">Employee Name</th>
          <th>User Name</th>
          <th>Time</th>
          <th>Date</th>
          <th>Outcome</th>
          <th>Remarks</th>
          <th>Conversion</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>

      </tbody>
      <tfoot>
        <tr>
          <th>#</th>
          <th class="admin-section">Employee Name</th>
          <th>User Name</th>
          <th>Time</th>
          <th>Date</th>
          <th>Outcome</th>
          <th>Remarks</th>
          <th>Conversion</th>
          <th>Actions</th>
        </tr>
      </tfoot>
    </table>
  </div>
  <!-- Add Call Logs -->
  <div class="modal fade" id="addCallLogsInput" tabindex="-1" aria-labelledby="inputModalLabel1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="inputModalLabel">Enter Call Logs</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="callLogsForm1">
            <div class="mb-3 admin-section">
              <label for="employeeName1" class="form-label">Choose Employee</label>
              <select name="employee_name" class="form-select" id="employeeName1"></select>
            </div>
            <div class="mb-3">
              <label for="userName1" class="form-label">Choose User</label>
              <select name="user_name" id="userName1" class="form-select"></select>
            </div>
            <div class="mb-3">
              <label for="callTime1" class="form-label">Choose Time For Call</label>
              <input type="time" id="callTime1" name="call_time" class="form-control">
            </div>
            <div class="mb-3">
              <label for="callDate1" class="form-label">Choose Date For Call</label>
              <input type="date" id="callDate1" name="call_date" class="form-control">
            </div>
            <div class="mb-3">
              <label for="outCome1" class="form-label">Choose Outcome For Call</label>
              <select name="outcome" id="outCome1" class="form-select">
                  <option value="">Select Outcome</option>
                  <option value="Reached">Reached</option>
                  <option value="No Answer">No Answer</option>
                  <option value="Left Voicemail">Left Voicemail</option>
                  <option value="Interested">Interested</option>
                  <option value="Not Interested">Not Interested</option>
                  <option value="Callback Requested">Callback Requested</option>
                  <option value="Converted">Converted</option>
                  <option value="Do Not Call">Do Not Call</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="remarks1" class="form-label">Enter Remarks for Call</label>
              <textarea id="remarks1" name="remarks" class="form-control">
              </textarea>
            </div>
            <div class="mb-3">
              <label for="conversion1" class="form-label">Choose Converion</label>
              <select class="form-select" name="conversion" id="conversion1">
                <option value="">Select Yes or No</option>
                <option value="1">Yes</option>
                <option value="0">No</option>
              </select>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button id="close" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button id="addCallLogsBTN" type="button" class="btn btn-primary">Add</button>
        </div>
      </div>
    </div>
  </div>
    <!-- Edit Call Logs -->
  <div class="modal fade" id="editCallLogsInput" tabindex="-1" aria-labelledby="inputModalLabel1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="inputModalLabel">Update Call Logs</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="callLogsForm2">
          <div class="mb-3 admin-section">
              <label for="employeeName2" class="form-label">Choose Employee</label>
              <select name="employee_name" class="form-select" id="employeeName2"></select>
            </div>
            <div class="mb-3">
              <label for="userName2" class="form-label">Choose User Name</label>
              <select name="user_name" id="userName2" class="form-select"></select>
            </div>
            <div class="mb-3">
              <label for="callTime2" class="form-label">Choose Time For Call</label>
              <input type="time" id="callTime2" name="call_time" class="form-control">
            </div>
            <div class="mb-3">
              <label for="callDate2" class="form-label">Choose Date For Call</label>
              <input type="date" id="callDate2" name="call_date" class="form-control">
            </div>
            <div class="mb-3">
              <label for="outCome2" class="form-label">Choose Outcome For Call</label>
              <select name="outcome" id="outCome2" class="form-select">
                  <option value="">Select Outcome</option>
                  <option value="Reached">Reached</option>
                  <option value="No Answer">No Answer</option>
                  <option value="Left Voicemail">Left Voicemail</option>
                  <option value="Interested">Interested</option>
                  <option value="Not Interested">Not Interested</option>
                  <option value="Callback Requested">Callback Requested</option>
                  <option value="Converted">Converted</option>
                  <option value="Do Not Call">Do Not Call</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="remarks2" class="form-label">Enter Remarks for Call</label>
              <textarea id="remarks2" name="remarks" class="form-control">
              </textarea>
            </div>
            <div class="mb-3">
              <label for="conversion2" class="form-label">Choose Converion</label>
              <select class="form-select" name="conversion" id="conversion2">
                <option value="">Select Yes or No</option>
                <option value="1">Yes</option>
                <option value="0">No</option>
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
  </div>
</main>
<?php
$script = "<script type='module' src='../assets/js/callLogs.js'></script>";
include("../components/footer.php");
?>