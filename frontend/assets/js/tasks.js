import { API_URL } from "../../config.js";

$("document").ready(function () {
  // ADD TASK
  const authToken = localStorage.getItem("authToken");
  const table = $("#taskTable").DataTable({
    ajax: {
      url: `${API_URL}/api/admin/getAllTask.php`,
      type: "GET",
      headers: {
        Authorization: `Bearer ${authToken}`,
        "Content-type": "application/json",
      },
    },
    dataSrc: function (json) {
      return json.data || [];
    },
    error: function (xhr, error, thrown) {
      $("#Tasktable").html(`<tr>
                <td colspan="8" class="text-center text-danger">Server error or no response from server. Please try again later.</td>
                </tr>`);
    },
    columns: [
      {
        data: null,
        render: function (data, type, row, meta) {
          return meta.row + 1;
        },
      },
      { data: "campaignName" },
      { data: "employeeName" },
      { data: "userName" },
      { data: "status" },
      { data: "action" },

    ],
    responsive: true,
    pagination: true,
  });
  //  GET ALL DATA
  async function getData(url) {
    if (url === "") {
      return null;
    }
    try {
      let data = await fetch(url, {
        method: "GET",
        headers: {
          Authorization: `Bearer ${authToken}`,
          "Content-type": "application/json",
        },
      });
      let response = await data.json();
      if (!response.success) {
        throw new Error("Server is Not Responding");
      }
      return response.data;
    } catch (error) {
      console.log(error);
    }
  }
  // POPULATE OPTIONS
  async function populateOptions(url, id, dataName) {
    if (!url || !id || !dataName) {
      return null;
    }
    let data = await getData(url);
    console.log(data);
    const select = $("#" + id);
    select.empty();
    select.append(`<option value="select">--Select--</>`);

    data.forEach((element) => {
      select.append(
        `<option value="${element.id}">${element[dataName]}</option>`
      );
    });
  }
  populateOptions(
    `${API_URL}/api/admin/getAllCampaign.php`,
    "campaignName1",
    "campaign_name"
  );
  populateOptions(
    `${API_URL}/api/admin/getAllEmployee.php`,
    "employeeName1",
    "name"
  );
  populateOptions(
    `${API_URL}/api/shared/getAllUsers.php`,
    "userName1",
    "name"
  );

  // ADD TASK
  async function addTask(data) {
    console.log(data);
    try{
        let respone = await fetch(`${API_URL}/api/admin/addTask.php`,{
            method: "POST",
            headers: {
                Authorization: `Bearer ${authToken}`,
                'Content-type': "application/json"
            },
            body: JSON.stringify(data)
        });
        respone = await respone.json();
        const modal = getModal("assignTaskInput");
        $("taskTable").ajax.reload();
        modal.hide();
        if(!respone.success) {
            Swal.fire({
                title: respone.message,
                icon: "danger"
            })
        } else {
            Swal.fire({
                title: respone.message,
                icon: "success"
            })
        }

    } catch(error) {
        console.error("Error: ",error)
    }
  }
  function getValue(name) {
    return name ? document.querySelector(name).value : null;
  }

  function getModal(modalId) {
    if (!modalId) {
      console.error("Modal Id is Empty");
    }
    try {
      return bootstrap.Modal.getInstance(document.getElementById(modalId));
    } catch (error) {
      console.error(error);
    }
  }
  $("#addTask").click(function () {
    let campaign_id = getValue("#campaignName1");
    let employee_id = getValue("#employeeName1");
    let user_id = getValue("#userName1");
    let status = getValue("#status1");
    let action = getValue("#action1");
    const modal = getModal("assignTaskInput");
    if (!campaign_id || !employee_id || !user_id || !status || !action) {
      modal.hide();
      
      Swal.fire({
        title: "Input Field is Empty!",
        icon: "warning",
      });
    } else {
      let text = "select";
      if (
        campaign_id === text ||
        employee_id === text ||
        user_id === text ||
        action === text
      ) {
        modal.hide();
        Swal.fire({
          title: "Input Field is Empty!",
          icon: "warning",
        });
      }
      let data = {
        campaign_id,
        employee_id,
        user_id,
        status,
        action
      }
      addTask(data);
    }
  });
});
