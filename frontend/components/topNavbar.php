 <!-- Top NavBar -->
 <div class="nav-bar bg-dark d-flex justify-content-between align-items-center p-4 fixed-top">
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
        <div class="position-relative search">
          <input class="form-control me-2 flex-shrink-0" type="search" aria-label="Search" id="searchBar">
          <i class="fa-solid fa-magnifying-glass" style="color: black;" id="search"></i>
        </div>
        <div>
          <i class="fa-solid fa-bell fs-5" style="color: #fff;"></i>
        </div>
        <div class="dropdown">
          <div class="nav-profile d-flex align-items-center cursor-pointer" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="../assets/images/img_avatar.png" alt="Profile Picture" height="40" class="rounded-circle">
            <span id="profile-name" class="text-white ms-2">Unknown</span>
          </div>
          <ul class="m-2 dropdown-menu dropdown-menu-end" aria-labelledby="profile-info">
            <li><a class="dropdown-item" href="#">Change Password</a></li>
            <li><a class="dropdown-item admin-section" href="#">Add Admin</a></li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item" href="#" id="logout">Logout</a></li>
          </ul>
        </div>
      </div>
    </div>