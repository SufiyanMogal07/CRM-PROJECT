import { getData, decodedToken } from "./helper/apiClient.js";

$(document).ready(function () {
  async function setWidgetCounter(endpoint) {
    try {
        let response = await getData(endpoint);
        let data = await response.data;
        
        data.forEach((counter) => {
            const [[key,value]] = Object.entries(counter); // Object.entries returns an array of array like this [[key,value]]
            document.getElementById(key).innerText = value;
        });
    } catch(error) {
        console.warn("Something Went Wrong While Populating Counters")
    }
  
  }

  let token = decodedToken(jwt_decode);
  let role = token.data.role;
  let url = "api/";

  if (role === "admin") {
    url += "admin/getAdminCounters.php";
  } else if (role === "employee") {
    url += "employee/getEmployeeCounters.php";
  }

  setWidgetCounter(url);
});
