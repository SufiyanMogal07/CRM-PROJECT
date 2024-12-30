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
      window.location.replace("http://127.0.0.1:3000/frontend/index.html");
    }
  });
});

grid.addEventListener("click", () => {
  sideNav.classList.toggle("expand");
});

sideNav.addEventListener("mouseenter", () => {
  sideNav.classList.add("expand");
});

sideNav.addEventListener("mouseleave", () => {
  sideNav.classList.remove("expand");
});

function validateEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}