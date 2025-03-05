<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <link rel="shortcut icon" href="assets/images/CRM_logo.png" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body>
  <div class="container-fluid text-white d-flex justify-content-center align-items-center">
    <div class="login-box bg-white h-auto w-auto">
      <form id="form" method="post" class="row flex-column justify-content-center">
        <h2 class="mb-2 text-center heading">Welcome, Back Again</h2>
        <h2 class="text-center fs-4 text-secondary-emphasis">Please Login In to Access Dashboard</h2>

        <div class="form-group pt-5">
          <label class="form-label mb-1 fs-semibold" for="emailInput">Email</label>
          <div class="input-box input-group mb-3">
            <span class="input-group-text"><i class="fa-solid fa-envelope" id="email-icon" style="color: #4a4a4a;"></i></span>
            <input class="form-control" type="email" placeholder="Enter your Email" id="emailInput"
              required aria-label="Email">
          </div>
        </div>
        <div class="form-group mt-2">
          <label class="form-label mb-1 fs-semibold" for="pass">Password</label>
          <div class="input-box input-group">
              <span class="input-group-text">
                <i class="fa-solid fa-lock" style="color: #4a4a4a;"></i>
              </span>
              <input class="form-control input-text" type="password" placeholder="Enter your Password" id="pass" required>
          </div>
        </div>
        <a class="mt-3 text-end" href="">Forgot Password ?</a>
        <button id="submit-btn" class="btn btn-primary mt-5 fs-5">Log In</button>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://kit.fontawesome.com/8a04af7d53.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script type="module" src="./assets/js/index.js"></script>
</body>

</html>