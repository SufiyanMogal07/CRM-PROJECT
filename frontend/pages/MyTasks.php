<?php
$page = "Tasks";
$css = '';
include("../layouts/dashboard_layout.php")
?>
<main class="main-body p-3 d-flex flex-column">
    <div class="main-container p-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h2 class="text-3xl">My Tasks</h2>
    </div>
    <div class="task-container table-container">
        <table id="myTaskTable" class="table table-striped table-bordered w-100">
            <thead>
                <tr>
                    <th>#TaskId</th>
                    <th>Campaign Name</th>
                    <th>User Name</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
                <tr>
                    <th>#TaskId</th>
                    <th>Campaign Name</th>
                    <th>User Name</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </tfoot>
        </table>
    </div>
    <?php include('../components/forms.php')?>
    </div>
</main>

<?php
$script = "<script type='module' src='../assets/js/mytasks.js'></script>";
include("../components/footer.php")
?>