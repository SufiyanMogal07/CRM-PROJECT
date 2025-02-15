<?php
$page = "Tasks";
$css = '';
include("../layouts/dashboard_layout.php")
?>
<main class="main-body p-3 d-flex flex-column">
    <div class="main-container p-5">
        <div class="d-flex justify-content-between align-items-center mb-5 top-head">
            <h2>Manage Tasks</h2>
            <div>
                <button id="reset" class="btn btn-danger">Reset</button>
                <button id="assignTask" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#assignTaskInput">Assign Task</button>
            </div>
        </div>
        <div class="task-container table-container">
            <table id="TaskTable" class="table table-bordered table-striped w-100">
                <thead>
                    <tr>
                        <th>#TaskId</th>
                        <th>Campaign Name</th>
                        <th>Employee Name</th>
                        <th>User Name</th>
                        <th>Status</th>
                        <th>Action</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                    <tr>
                        <th>#TaskId</th>
                        <th>Campaign Name</th>
                        <th>Employee Name</th>
                        <th>User Name</th>
                        <th>Status</th>
                        <th>Action</th>
                        <th>Actions</th>
                    </tr>
                </tfoot>
            </table>
            <!-- Add Task -->
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
                                    <label class="form-label" for="action1">Enter Task Action</label>
                                    <textarea class="form-control" name="action1" id="action1"></textarea>
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
            <!-- Edit Task -->
            <div class="modal fade" id="editTaskInput" tabindex="-1" aria-labelledby="inputModalLabel" aria-hidden="true">
                <div class="modal-dialog  modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="inputModalLabel">Enter Task Details</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form id="taskForm2">
                                <div class="mb-3">
                                    <label class="form-label" for="campaignName2">Select Campaign Name</label>
                                    <select name="campaignName2" id="campaignName2" class="form-control">
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="employeeName2">Select Employee Name</label>
                                    <select name="employeeName2" id="employeeName2" class="form-control">
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="userName2">Select User Name</label>
                                    <select name="userName2" id="userName2" class="form-control">
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="status" class="form-label">Select Status For Task</label>
                                    <select class="form-select w-100" name="status" id="status">
                                        <option value="none">Select</option>
                                        <option value="Pending">Pending</option>
                                        <option value="In Progress">In Progress</option>
                                        <option value="Completed">Completed</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="action2">Enter Task Action</label>
                                    <textarea class="form-control" name="action2" id="action2"></textarea>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button id="close" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button id="saveChanges" type="button" class="btn btn-primary">Save Changes</button>
                        </div>
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