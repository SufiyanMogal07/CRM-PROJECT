import { authToken, decodedToken, deleteData, updateData } from './assets/js/helper/apiClient.js'
import { getData } from './assets/js/helper/apiClient.js';
Pusher.logToConsole = false;

let pusher = new Pusher('5a2db22522b4ecb95c95', {
  cluster: 'ap2',
  authEndpoint: 'backend/pusher_auth.php',
  auth: {
    headers: {
      'Authorization': `Bearer ${authToken}`
    }
  }
});

const token = decodedToken(jwt_decode)
const role = token.data.role;
const id = token.data.id;
let notification_dropdown = document.querySelector(".dropdown-menu-lg .notification-list");
let notification_counter = document.querySelector("#message-counter");
let counter = 0;


let channel = pusher.subscribe("notifications");
let event = "";

if (role === "employee") {
  event = "private-employee-" + id;
} else if (role === "admin") {
  event = "private-admin-" + id;
}

function requestPermission() {
  Notification.requestPermission().then((permission) => {
    if (permission === "granted") {
      console.log("Permission Granted!!!");
      return true;
    } else {
      console.log("Permission Denied!!!");
      return false;
    }
  })
}

async function addPushNotification(data) {
  const iconUrl = window.location.origin + '/CRM_PROJECT/frontend/assets/images/CRM_logo.png';

  if (Notification.permission === "granted") {
    new Notification("New Message", {
      body: data.message,
      icon: iconUrl
    });
  } else if (Notification.permission === "denied") {
    let permission = requestPermission();
    if (permission) {
      new Notification("New Message", {
        body: data.message,
        icon: iconUrl
      });
    }

  }
}

async function getNotifications() {
  let response = await getData("api/notifications/getNotifications.php");
  return Array.from(response.data) ?? [];
}

async function addUiNotification() {
  let data = await getNotifications();
  let length = data.length;
  counter = 0;

  if (length > 0) {
    notification_dropdown.innerHTML = "";
    data.forEach((value) => {
      notification_dropdown.innerHTML += (`<li><p class="dropdown-item d-flex" data-id=${value.id}>${value.message}<small>${value.created_at}</small> <i class="fa-solid fa-xmark"></i></p></li>`);
      counter++;
    });
    notification_counter.innerHTML = counter;
  } else {
    notification_dropdown.innerHTML ='<li class="parent-notify"><p class="text-center" id="no-notification">You have <span class="text-danger">0</span> new notifications</p></li>';
    notification_counter.innerHTML = 0;
  }
}

async function markAsRead(id) {
  let data = {id}
  let response = await updateData(`api/notifications/updateNotification.php`,data);
  if (!response) {
    console.warn(response.message);
    return false;
  }
  return response.success;
}

// Remove Notifications
$(".dropdown-menu-lg").on("click", ".dropdown-item > i", function () {
  let currentElement = this.parentElement.parentElement;
  let element = $(this).closest('.dropdown-item');
  let id = element.data("id");

  currentElement.classList.add("animate__animated", "animate__backOutRight");
  if (counter >= 1) {
    currentElement.addEventListener("animationend", function () {
      let flag = markAsRead(id);
      if (flag) {
        currentElement.remove();
        --counter;
        notification_counter.innerHTML = counter;
        if (counter === 0) {
          console.log("Inside")
          notification_dropdown.innerHTML = '<li class="parent-notify"><p class="text-center" id="no-notification">You have <span class="text-danger">0</span> new notifications</p></li>';
        }
      } else {
        SwalPopup(Swal,flag.message,"error");
      }
    });
  }
});


// Binding Pusher
requestPermission();
channel.bind(event, function (data) {
  setTimeout(()=> {
    window.location.reload();
  },400)

  addPushNotification(data);
  addUiNotification();
})


addUiNotification();

