import { FRONTEND_URL,LOGIN_URL } from "../../config.js";

window.onload = function () {
  if(!localStorage.getItem("authToken")) {
    window.location.replace (`${LOGIN_URL}`);
    return;
  }
}

const wrapper = document.querySelector(".wrapper");
const adminSection = document.querySelectorAll(".admin-section");
const employeeSection = document.querySelectorAll(".employee-section");

const token = localStorage.getItem("authToken");
const urls = {
  login: `${LOGIN_URL}`,
  crm: `${FRONTEND_URL}pages/dashboard.php`,
};

if (token) {
  try {
    const decodedToken = jwt_decode(token);
    const currentTime = Math.floor(Date.now() / 1000);
    if (decodedToken.exp < currentTime) {
        Swal.fire({
            title: "Session expired!!",
            text: "Please Login Again",
            icon: "error"
        });
      localStorage.removeItem("authToken");
      window.location.href = login;
    } else {
      document.body.style.display ="block"
        const { role } = decodedToken.data;
        const {name} = decodedToken.data;

        document.querySelector("#profile-name").innerHTML = name;
        if(role!=="admin" && role!=="employee") {
          alert("Unauthorized Role!!");
          window.location.href = urls.login;
        }
        if (role === "employee") {
          adminSection.forEach((section)=> section.remove());
          
        }
        if(role==="admin") {
          employeeSection.forEach((section)=> section.remove());
        }
      let dbRole = document.querySelector("#dashboard-role");
      if(dbRole) {
        dbRole.innerHTML = `${role} dashboard`.toUpperCase();
      }
      wrapper.style.display = "block";
    }
  } catch (error) {
    console.error("Invalid Token", error);
    localStorage.removeItem("authToken");
    window.location.replace(urls.login);
  }
}

