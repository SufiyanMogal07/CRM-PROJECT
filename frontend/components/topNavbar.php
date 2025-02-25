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
             <span id="message-counter">0</span>
           </span>
         </i>
        </a>
         <ul class="dropdown-menu dropdown-menu-end dropdown-menu-lg" aria-labelledby="notification-dropdown">
          <li><h6 class="dropdown-header">Notifications</h6></li>
          <li><p class="dropdown-divider"></p></li>
          <div class="notification-list">
            
          </div>
         </ul>
     </div>

     <div class="dropdown">
       <div class="nav-profile d-flex align-items-center dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
         <img src="../assets/images/img_avatar.png" alt="Profile Picture" height="40" class="rounded-circle">
         <span id="profile-name" class="text-white ms-2">Unknown</span>
       </div>
       <ul class="m-2 dropdown-menu" aria-labelledby="profile-info">
         <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#changePassword">Change Password</a></li>
         <li><a class="dropdown-item admin-section" data-bs-toggle="modal" data-bs-target="#addAdmin">Add Admin</a></li>
         <li>
           <hr class="dropdown-divider">
         </li>
         <li><a class="dropdown-item" href="#" id="logout">Logout</a></li>
       </ul>
     </div>
   </div>
 </div>