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
  <div class="container-fluid text-white d-flex justify-content-center align-items-center p-0">

    <div class="login-box bg-white h-40 rounded-xl">
      <form id="form" method="post" class="d-flex flex-column justify-content-center">
        <h2 class="mb-5 text-center heading text-uppercase">Sign In Page</h2>
        <div class="d-flex flex-column text-black mb-2 mt-2">
          <label class="form-label mb-1 fs-5 fw-semibold mb-3" for="emailInput">Enter Your Email</label>
          <div class="input-box">
            <i class="fa-solid fa-envelope"></i>
            <input class="form-control mb-2 input-text border-red" type="email" placeholder="Email" id="emailInput"
              required>
          </div>

        </div>
        <div class="d-flex flex-column text-black mb-2">
          <label class="form-label mb-1 fs-5 fw-semibold mb-3" for="pass">Enter Your Password</label>
          <div class="input-box">
            <i class="fa-solid fa-lock"></i>
            <input class="form-control mb-2 input-text" type="password" placeholder="Password" id="pass" required>
          </div>
        </div>
        <button id="submit-btn" class="btn btn-primary mt-5 fs-5">Submit</button>
      </form>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://kit.fontawesome.com/8a04af7d53.js" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script type="module" src="./assets/js/index.js"></script>
</body>

</html>