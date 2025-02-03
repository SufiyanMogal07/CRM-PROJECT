import { BASE_URL,API_URL } from "../../config.js";


if (localStorage.getItem("authToken")) {
  window.location.href = `${BASE_URL}pages/CRMDashboard.php`;
}

const email = document.querySelector("#emailInput");
const pass = document.querySelector("#pass");
const submit = document.querySelector("#submit-btn");

let emailValue = "";
let passValue = "";

function validateEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

email.addEventListener("input", function (e) {
  emailValue = e.target.value;
});

pass.addEventListener("input", function (e) {
  passValue = e.target.value;
});

submit.addEventListener("click", (e) => {
  e.preventDefault();
  if (!emailValue && !passValue) {
    alert("Please Fill All Fields!!!");
  } else {
    if (!validateEmail(emailValue)) {
      alert("Email is Not in Valid Format");
      return;
    }
    if (passValue.length < 4) {
      alert("Password should be greater than 4 characters");
    } else {
      const data = {
        email: emailValue,
        password: passValue,
      };
      fetch(`${API_URL}auth/login.php`, {
        method: "POST",
        headers: {
          "Content-type": "application/json",
        },
        body: JSON.stringify(data),
      })
        .then((response) => {
          if (!response.ok) {
            throw new Error("Network response was not ok");
          }
          return response.json();
        })
        .then((data) => {
          if (data.success) {
            localStorage.setItem("authToken", data.token);
            window.location.href =
              `${BASE_URL}pages/CRMDashboard.php`;
          } else {
            Swal.fire({
              title: data.message,
              icon: "warning"
            })
          }
          setTimeout(() => {
            email.value = "";
            pass.value = "";
          }, 300);
        })
        .catch((error) => {
          Swal.fire({
            title: "Server is Not Responding!!!",
            text: error,
            icon: "warning"
          })

          setTimeout(() => {
            email.value = "";
            pass.value = "";
          }, 300);
        });
    }
  }
});

export default validateEmail;