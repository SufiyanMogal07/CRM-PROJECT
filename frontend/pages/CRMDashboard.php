<?php 
$page = "Home";
$css = '<link rel="stylesheet" href="../assets/css/main.css">';
include("../layouts/dashboard_layout.php");
?>

<main class="main-body d-flex flex-column p-3">
  <div class="main-container p-3">
    <h2 id="dashboard-role" class="p-3">Dashboard</h2>
    <div class="dashboard-widgets d-flex">
      <a href="./Employee.php" target="_blank" class="dashboard-widget bg-primary p-5 text-center text-white admin-section">
        <div>
          <h1 class="fw-bolder fs-2">150</h1>
          <p>Total Employees</p>
        </div>
        <div>
          <i class="fas fa-user-tie"></i>
        </div>
      </a>
      <a href="./User.php" target="_blank" class="dashboard-widget bg-secondary p-5 text-center text-white">
        <div>
          <h1 class="fw-bolder fs-2 ">150</h1>
          <p>Total Users</p>
        </div>
        <div>
          <i class="fa-solid fa-user"></i>
        </div>
      </a>
      <a href="./Campaigns.php" target="_blank" class="dashboard-widget bg-warning p-5 text-center text-white">
        <div>
          <h1 class="fw-bolder fs-2">150</h1>
          <p>Total Campaigns</p>
        </div>
        <div>
          <i class="fa fa-tasks"></i>
        </div>
      </a>
      <a href="./Tasks.php" target="_blank" class="dashboard-widget bg-danger p-5 text-center text-white admin-section">
        <div>
          <h1 class="fw-bolder fs-2">150</h1>
          <p>Pending Tasks</p>
        </div>
        <div>
          <i class="fa-solid fa-list-check"></i>
        </div>
      </a>
      <a href="./MyTasks.php" target="_blank" class="dashboard-widget bg-danger p-5 text-center text-white employee-section">
        <div>
          <h1 class="fw-bolder fs-2">150</h1>
          <p>Pending Tasks</p>
        </div>
        <div>
        <i class="fa-solid fa-square-phone"></i>
        </div>
      </a>
      <a href="./CallLogs.php" target="_blank" class="dashboard-widget bg-success p-5 text-center text-white">
        <div>
          <h1 class="fw-bolder fs-2">150</h1>
          <p>Call Logs</p>
        </div>
        <div>
        <i class="fa-solid fa-square-phone"></i>
        </div>
      </a>
    </div>
  </div>
</main>

<?php
$script="";
include("../components/footer.php");
?>