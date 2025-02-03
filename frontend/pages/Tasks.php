<?php
  $page = "Tasks";
  $css = '';
  include("../layouts/dashboard_layout.php")
?>
<main class="main-body p-5 d-flex flex-column">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h2>Manage Tasks</h2>
        <div>
            <button id="reset" class="btn btn-danger">Reset</button>
            <button id="assignTask" class="btn btn-primary" data-bs-toggle="modal"
                data-bs-target="#assignTaskInput">Assign Task</button>
        </div>
    </div>
    <div class="task-container">
        <table id="taskTable" class="table table-striped w-100">
            <thead>
                <th>#TaskId</th>
                <th>Campaign Name</th>
                <th>Employee Name</th>
                <th>User Name</th>
                <th>Status</th>
                <th>Action</th>
            </thead>
            <tbody></tbody>
        </table>
        <div class="modal fade" id="assignTaskInput" tabindex="-1" aria-labelledby="inputModalLabel" aria-hidden="true">
            <div class="modal-dialog  modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="inputModalLabel">Enter Task Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="taskForm">
                            <div class="mb-3">
                                <label class="form-label" for="campaignName1">Select Campaign Name</label>
                                <select name="campaignName1" id="campaignName1" class="form-control">
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="employeeName1">Select Employee Name</label>
                                <select name="employeeName1" id="employeeName1" class="form-control">
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="userName1">Select User Name</label>
                                <select name="userName1" id="userName1" class="form-control">
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="status1" class="form-label">Select Task Status</label>
                                <select name="status1" id="status1" class="form-control">
                                    <option value="select">--Select--</option>
                                    <option value="Pending">Pending</option>
                                    <option value="In Progress">In Progress</option>
                                    <option value="Completed">Completed</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="action1">Enter Task Action</label>
                                <input class="form-control" type="text" name="action1" id="action1" placeholder="Action">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button id="close" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button id="addTask" type="button" class="btn btn-primary">Add Task</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
  $script = "<script type='module' src='../assets/js/tasks.js'></script>";
  include("../components/footer.php")
?>
