 <!-- Top NavBar -->
 <div class="nav-bar bg-dark d-flex justify-content-between align-items-center p-4 fixed-top left-0 right-0">
   <div class="d-flex align-items-center gap-4">
     <div class="navbar-grid" id="grid-icon">
       <div class="navbar-line"></div>
       <div class="navbar-line"></div>
       <div class="navbar-line"></div>
     </div>
     <div class="heading-logo fw-bolder fs-5 text-white ">
       CRM<span class="text-primary">DASHBOARD</span>
     </div>
   </div>

   <div class="d-flex align-items-center gap-4">

     <div class="dropdown">
       <a id="notification-dropdown" href="#" data-bs-auto-close="outside" data-bs-toggle="dropdown" aria-expanded="false">
         <i class="fas fa-bell fs-5 position-relative" style="color: #fff;">
           <span style="font-size: 0.5rem; min-width: 16px; padding: 3px 5px;" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
             0
             <span class="visually-hidden">unread messages</span>
           </span>
         </i>
        </a>
         <ul class="dropdown-menu dropdown-menu-end dropdown-menu-lg" aria-labelledby="notification-dropdown">
          <li><h6 class="dropdown-header">Notifications</h6></li>
          <li><p class="dropdown-divider"></p></li>
          <li><p class="text-center" id="no-notification">You have <span class="text-danger">0</span> new notifications</p></li>
          <li><p class="dropdown-item">A new Task has Assigned to You A new Task has <i class="fa-solid fa-xmark"></i></p></li>
          <li><p class="dropdown-item">A new Task has Assigned to You  <i class="fa-solid fa-xmark"></i></p></li>
         </ul>
     </div>

     <div class="dropdown">
       <div class="nav-profile d-flex align-items-center dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
         <img src="../assets/images/img_avatar.png" alt="Profile Picture" height="40" class="rounded-circle">
         <span id="profile-name" class="text-white ms-2">Unknown</span>
       </div>
       <ul class="m-2 dropdown-menu" aria-labelledby="profile-info">
         <li><a class="dropdown-item" href="../pages/changePassword.php">Change Password</a></li>
         <li><a class="dropdown-item admin-section" href="../pages/addAdmin.php">Add Admin</a></li>
         <li>
           <hr class="dropdown-divider">
         </li>
         <li><a class="dropdown-item" href="#" id="logout">Logout</a></li>
       </ul>
     </div>
   </div>
 </div>