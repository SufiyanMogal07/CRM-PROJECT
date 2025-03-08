<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <link rel="shortcut icon" href="./frontend/assets/images/CRM_logo.png" type="image/x-icon">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="./frontend/assets/css/style.css">
</head>

<body>
  <div class="container-fluid text-white d-flex justify-content-center align-items-center">
    <div class="login-box bg-white h-auto w-auto">
      <form id="form" method="post" class="row flex-column justify-content-center align-items-center">
        <h2 class="mb-2 text-center text-black fs-3">Welcome, Back Again</h2>
        <h2 class="text-center fs-5 text-secondary-emphasis">Please Login In to Access Dashboard</h2>

        <div class="form-group pt-5">
          <div class="input-box input-group mb-3">
            <span class="input-group-text"><i class="fa-solid fa-envelope" id="email-icon"></i></span>
            <input class="form-control" type="email" placeholder="Enter your Email" id="emailInput"
              required aria-label="Email">
          </div>
        </div>
        <div class="form-group mt-3">
          <div class="input-box input-group">
              <span class="input-group-text">
                <i class="fa-solid fa-lock"></i>
              </span>
              <input class="form-control input-text border" type="password" placeholder="Enter your Password" id="pass" required>
          </div>
        </div>
        <a class="mt-4 text-end" href="">Forgot Password ?</a>
        <button id="submit-btn" class="btn btn-primary mt-4 fs-5 w-50 rounded-2">Log In</button>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://kit.fontawesome.com/8a04af7d53.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script type="module" src="./frontend/assets/js/index.js"></script>
</body>

</html>