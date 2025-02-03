<?php 
$page = "Home";
$css = '<link rel="stylesheet" href="../assets/css/main.css">';
include("../layouts/dashboard_layout.php");
?>

<main class="main-body p-3 d-flex flex-column">
  <div class="main-container">
    <h2 id="dashboard-role" class="p-3">Dashboard</h2>
    <div class="dashboard-widgets d-flex gap-4 align-items-start admin-section">
      <a href="./Employee.html" class="dashboard-widget bg-primary p-5 text-center text-white">
        <div>
          <h1 class="fw-bolder fs-2">150</h1>
          <p>Total Employees</p>
        </div>
        <div>
          <i class="fas fa-user-tie"></i>
        </div>
      </a>
      <a href="./Campaigns.html" class="dashboard-widget bg-danger p-5 text-center text-white">
        <div>
          <h1 class="fw-bolder fs-2">150</h1>
          <p>Active Campaigns</p>
        </div>
        <div>
          <i class="fa fa-tasks"></i>
        </div>
      </a>
      <a href="./Tasks.html" class="dashboard-widget bg-success p-5 text-center text-white">
        <div>
          <h1 class="fw-bolder fs-2 ">150</h1>
          <p>Pending Tasks</p>
        </div>
        <div>
          <i class="fas fa-tasks"></i>
        </div>
      </a>
      <div class="dashboard-widget bg-secondary p-5 text-center text-white">
        <div>
          <h1 class="fw-bolder fs-2 ">150</h1>
          <p>Total Users</p>
        </div>
        <div>
          <i class="fa-solid fa-user-group"></i>
        </div>
      </div>
      <div class="dashboard-widget bg-warning p-5 text-center text-white">
        <div>
          <h1 class="fw-bolder fs-2 ">150</h1>
          <p>Completed Calls</p>
        </div>
        <div>
          <i class="fa-solid fa-square-phone"></i>
        </div>
      </div>
    </div>
    <!-- <div class="dashboard-widgets d-flex gap-4 align-items-start employee-section">
      <a href="./Employee.html" class="dashboard-widget bg-primary p-5 text-center text-white">
        <div>
          <h1 class="fw-bolder fs-2">150</h1>
          <p>Total Employees</p>
        </div>
        <div>
          <i class="fas fa-user"></i>
        </div>
      </a>
      <a href="./Campaigns.html" class="dashboard-widget bg-danger p-5 text-center text-white">
        <div>
          <h1 class="fw-bolder fs-2">150</h1>
          <p>Active Campaigns</p>
        </div>
        <div>
          <i class="fas fa-user-tie"></i>
        </div>
      </a>
      <a href="./Tasks.html" class="dashboard-widget bg-success p-5 text-center text-white">
        <div>
          <h1 class="fw-bolder fs-2 ">150</h1>
          <p>Pending Tasks</p>
        </div>
        <div>
          <i class="fas fa-tasks"></i>
        </div>
      </a>
      <div class="dashboard-widget bg-secondary p-5 text-center text-white">
        <div>
          <h1 class="fw-bolder fs-2 ">150</h1>
          <p>Completed Calls</p>
        </div>
        <div>
          <i class="fa-solid fa-square-phone"></i>
        </div>
      </div>
      <div class="dashboard-widget bg-warning p-5 text-center text-white">
        <div>
          <h1 class="fw-bolder fs-2 ">150</h1>
          <p>Completed Calls</p>
        </div>
        <div>
          <i class="fa-solid fa-square-phone"></i>
        </div>
      </div>
    </div> -->
  </div>
</main>

<?php
$script = '';
include("../components/footer.php");
?>