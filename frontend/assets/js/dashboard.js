import { BASE_URL } from "../../config.js";
import { addData, updateData } from './helper/apiClient.js'
import {getElementValue,getBootStrapModal, SwalPopup} from './helper/uiHelper.js'
const grid = document.querySelector("#grid-icon");
const sideNav = document.querySelector("#side-nav");
const currentLocation = window.location.href;
const navLinks = document.querySelectorAll(".nav-link");

navLinks.forEach((links)=> {
    if(links.href.includes(currentLocation)) {
      sideNav.classList.add("expand");
      links.classList.add("active")
    }
  })

document.querySelector("#logout").addEventListener("click", () => {
  Swal.fire({
    title: "Do you really want to Logout?",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Logout",
  }).then((result) => {
    if (result.isConfirmed) {
      localStorage.removeItem("authToken");
      window.location.replace(`${BASE_URL}/index.php`);
    }
  });
});

grid.addEventListener("click", () => {
  sideNav.classList.toggle("expand");
});

// sideNav.addEventListener("mouseenter", () => {
//   sideNav.classList.add("expand");
// });

sideNav.addEventListener("mouseleave", () => {
  sideNav.classList.remove("expand");
});

function validateEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}
function clearForm(n = 2) {
  if(n === 1) {
    $("#adminName").val("");
    $("#adminEmail").val("");
    $("#adminPhone").val("");
    $("#adminPassword").val("");
  } else if(n === 2) {
    $("#oldPassword").val("");
    $("#newPassword").val("");
    $("#confirmPassword").val("");
  } else {
    console.error("No argument passed to clear form function!!");
  }
}

async function addAdmin() {
  let modal = getBootStrapModal("addAdmin");
  let name = getElementValue("adminName");
  let email = getElementValue("adminEmail");
  let phone = getElementValue("adminPhone");
  let password = getElementValue("adminPassword");

  if(name!=="" && email!=="" && phone!="" && password!=="") {
    let data = {
      name,email,phone,password
    }
    console.log(data);
    if(validateEmail(email)) {
      let response = await addData('api/admin/addAdmin.php',data);
      if(response.success) {
        SwalPopup(Swal,response.message,'success');
        clearForm(1);
        modal.hide();
      } else {
        SwalPopup(Swal,response.message,'error');
        clearForm(1);
        modal.hide();
      }
    } else {
      SwalPopup(Swal,"Email is Invalid","error");
    }
  } else {
    SwalPopup(Swal,"Input is Empty!!","error");
  }
} 

async function changePassword() {
  let modal = getBootStrapModal("changePassword");
  let old_password = getElementValue("oldPassword") ?? "";
  let password = getElementValue("newPassword") ?? "";
  let confirmPassword = getElementValue("confirmPassword") ?? "";

  let data = {
    old_password,
    password
  }

  if(old_password ==="" || password ==="" || confirmPassword ==="") {
    SwalPopup(Swal,"Input Field is Empty!!!","info");
  } else {
    if(password===confirmPassword) {
      let response = await updateData("api/shared/changePassword.php",data);
      if (response.success) {
        SwalPopup(Swal,response.message,"success");
        clearForm();
        modal.hide();
      } else {
        SwalPopup(Swal,response.message,"error");
        clearForm();
        modal.hide();
      }
    } else {
      SwalPopup(Swal,"New Password and Confirm Password Not Matching!!!","error");
    }
  }
}

$("#addAdmin .btn-close,#addAdmin #close").click(()=> clearForm(1));
$("#changePassword .btn-close,#changePassword #close").click(()=> clearForm());

// Add Admin and Change Password
$("#addAdminBTN").click(addAdmin);
$("#changePasswordBTN").click(changePassword);