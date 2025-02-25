import { initDataTable } from "./helper/DataTableHelper.js";
import { getActionbuttons, getBootStrapModal, getElementValue, SwalPopup,deleteSwalPopup } from "./helper/uiHelper.js";
import { addData, decodedToken, deleteData, getData, updateData } from "./helper/apiClient.js";
import { BASE_URL } from "../../config.js";


let table;
$(document).ready(function () {

  let token = decodedToken(jwt_decode);
  let role = token.data.role;
  let crm =  `${BASE_URL}pages/CRMDashboard.php`;
  let url = "api/tasks/"

  if(role!=="admin") {
    window.location.href = crm;
  }
  
  table = initDataTable("#TaskTable",`${url}getAllTask.php`,[
    {
      data: null,
      render: function (data, type, row, meta) {
        return meta.row + 1;
      },
    },
    { data: "campaignName" },
    { data: "employeeName" },
    { data: "userName" },
    { data: null,
      title: "Status",
      render: function (data,type,row) {
        let status = data.status;
        let color = "";
        if (status === "Pending") {
          color = "danger";
        } else if (status === "In Progress") {
          color = "warning";
        } else if (status === "Completed") {
          color = "success";
        }
    
        return `<span class="badge px-3 py-1 text-bg-${color}">${status.toUpperCase()}</span>`;
      }
    },
    { data: "action" },
    {
      data: null,
      title: "Actions",
      render: function (data,type,row) {
        let token = decodedToken(jwt_decode);
        let role = token.data.role;
        if(role==="admin") {
          return getActionbuttons("editTask","editTaskInput","deleteTask",data.taskID);
        } else {
          return `<span class="text-muted">No Actions Availble</span>`;
        }
      }
    }
  ]);

  
  // POPULATE OPTIONS
  async function populateOptions(url, selector, dataName) {
    if (!url || !selector || !dataName) {
      return null;
    }
    let respone = await getData(url);
    if(respone.success) {
      respone = respone.data;
      const select = $("#" + selector);
      select.empty();
      select.append(`<option value="" selected>Select</>`);

      respone.forEach((element) => {
        select.append(
          `<option value="${element.id}">${element[dataName]}</option>`
        );
      });
    } else {
      const select = $("#" + selector);
      select.empty();
      select.append(`<option value="" selected>Select</>`);
      console.log(respone);
    }
  }
  function clearForm(n = 2) {
    $(`#campaignName${n}`).val("");
    $(`#employeeName${n}`).val("");
    $(`#userName${n}`).val("");
    $(`#action${n}`).val("");
    if(n==2) {
      $(`#status`).val("");
    }
  }
    populateOptions(
      `/api/campaigns/getAllCampaign.php`,
      "campaignName1",
      "campaign_name",
    );
    populateOptions(
      `/api/employee/getAllEmployee.php`,
      "employeeName1",
      "name",
    );
    populateOptions(
      `/api/users/getAllUsers.php`,
      "userName1",
      "name",
    );


  // ADD TASK
  async function addTask(data) {
    let respone = await addData(`${url}addTask.php`,data);
    let modal = getBootStrapModal("assignTaskInput");
    if(respone.success) {
      SwalPopup(Swal,respone.message,"success");
    } else {
      SwalPopup(Swal,data.message,"error");
    }
    modal.hide();
    table.ajax.reload(null,false);
    clearForm(1);
  }

  $("#addTask").click(function () {
    let campaign_id = getElementValue("campaignName1");
    let employee_id = getElementValue("employeeName1");
    let user_id = getElementValue("userName1");
    let action = getElementValue("action1");
    const modal = getBootStrapModal("assignTaskInput");
    
    if (!campaign_id || !employee_id || !user_id || !action) {
      modal.hide();
      SwalPopup(Swal,"Input Field is Empty!","warning");
    } else {
      let text = "";
      if (
        campaign_id === text ||
        employee_id === text ||
        user_id === text ||
        action === text
      ) {
        modal.hide();
        SwalPopup(Swal,"Input Field is Empty!","warning");
        return 
      }
      let data = {
        campaign_id,
        employee_id,
        user_id,
        action
      }
      addTask(data);
    }
  });

  // Edit Task
  async function updateTask(data) {
    if(!data) return console.warn("Data is Empty!!");
    let respone = await updateData(`${url}updateTask.php`,data);
    let modal = getBootStrapModal("editTaskInput");

    if(respone.success) {
      SwalPopup(Swal,respone.message,"success");
    } else {
      SwalPopup(Swal,respone.message,"error");
    }
    modal.hide();
    table.ajax.reload();
    clearForm();
  }
  $("#saveChanges").click(function() {
    let id = $(this).attr("data-id");
    let status = getElementValue("status");
    let action = getElementValue("action2");
    const modal = getBootStrapModal("editTaskInput");

    if (!status|| !action ) {
      modal.hide();
      SwalPopup(Swal,"Input Field is Empty!","warning");
    } else {
      let text = "";
      if (status === text) {
        modal.hide();
        SwalPopup(Swal,"Status Not Selected","warning");
        return 
      }
      let data = {
        id,
        status,
        action
      }
      updateTask(data);
    }
  });

  $("#TaskTable").on("click","#editTask",function () {
    let id = $(this).attr("data-id");
    let rowData = table.rows().data().toArray();
    console.log(rowData);
    rowData = rowData.filter((item)=> item.taskID == id)[0];
    
    Promise.all([
      populateOptions(`/api/campaigns/getAllCampaign.php`, "campaignName2", "campaign_name"),
      populateOptions(`/api/employee/getAllEmployee.php`, "employeeName2", "name"),
      populateOptions(`/api/users/getAllUsers.php`, "userName2", "name")
    ]).then(() => {
      // Now that the options are populated, set the selected options:
      document.querySelector(
        `#employeeName2 option[value="${rowData.employeeId}"]`
      ).selected = true;
      document.querySelector(
        `#campaignName2 option[value="${rowData.campaignId}"]`
      ).selected = true;
      document.querySelector(
        `#userName2 option[value="${rowData.userId}"]`
      ).selected = true;
      document.querySelector("#employeeName2").disabled = true;
      document.querySelector("#campaignName2").disabled = true;
      document.querySelector("#userName2").disabled = true;
    });

    document.querySelector(`#status option[value="${rowData.status}`).selected = true;
    document.querySelector("#action2").value = rowData.action;
    $("#saveChanges").attr('data-id',id);
  });

  // Delete Task

     async function deleteTask(id) {
        if(id) {
          let data = await deleteData(`${url}deleteTask.php?id=${id}`);
          let icon = data.success ? "success": "error";
          SwalPopup(Swal,data.message,icon);
          table.ajax.reload(null,false);
        } else {
          console.error("Id is Missing!!!")
        }
      }

  $("#TaskTable").on("click","#deleteTask",function () {
     deleteSwalPopup(Swal).then((result) => {
            if (result.isConfirmed) {
              let id = $(this).attr("data-id");
              deleteTask(id);
            }
          });
  });

  $("#assignTaskInput #close,#assignTaskInput .btn-close").click(
    function () {
      clearForm(1);
    }
  );
  $("#editAttendanceInput #close,#editAttendanceInput .btn-close").click(
    function () {
      clearForm();
    }
  );

  // Reload Table
  $("#reset").click(function () {
    table.ajax.reload(null, false);
  });
});
