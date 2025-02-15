import { deleteSwalPopup, getActionbuttons, getBootStrapModal, getElementValue, SwalPopup } from "./helper/uiHelper.js";
import { initDataTable } from "./helper/DataTableHelper.js";
import {decodedToken,getData,addData,updateData,deleteData} from './helper/apiClient.js'
import { BASE_URL } from "../../config.js";

let table;
$(document).ready(function () {
   let token = decodedToken(jwt_decode);
  let role = token.data.role
  let crm =  `${BASE_URL}pages/CRMDashboard.php`

  if(role!=="admin") {
    window.location.href = crm;
  }
  table = initDataTable(
    "#AttendanceTable",
    "api/admin/getAllAttendance.php",
    [
      {
        data: null,
        render: function (data, type, row, meta) {
          return meta.row + 1;
        },
      },
      {
        data: null,
        render: function (data) {
          return `<span id="${data.employee_id}">${data.employee_name}</span>`;
        },
      },
      { data: "attendance_date" },
      {
        data: null,
        render: function (data, type, row) {
          let status = data.attendance_status;
          let color = "";
          if (status === "present") {
            color = "success";
          } else if (status === "absent") {
            color = "danger";
          } else if (status === "leave") {
            color = "warning";
          }
          return `<span class="badge rounded-pill px-3 py-2 text-bg-${color}">${status.toUpperCase()}</span>`;
        },
      },
      {
        data: null,
        title: "Actions",
        render: function (data, type, row) {
          let role = decodedToken(jwt_decode).data.role;
          if (role === "admin") {
            return getActionbuttons("editAtt","editAttendanceInput","deleteAtt",row.attendance_id,`data-employee-id = ${row.employee_id}`);
          } else {
            return `<span class="text-muted">No Actions Availble</span>`;
          }
        },
      },
    ],
    "Server error or no response from server. Please try again later."
  );


  async function populateName(n = 2) {
    let employee = await getData("api/admin/getAllEmployee.php");
    employee = await employee.data;
    if(employee) {
      const employeename = document.querySelector(`#employeeName${n}`);
      employeename.innerHTML += employee.map((item) => {
        return `<option value="${item.id}">${item.name}</option>`;
      });
    }

  }

  function clearForm(n = 2) {
    $(`#employeeName${n}`).val("");
    $(`#employeeDate${n}`).val("");
    $(`#employeeStatus${n}`).val("");
  }
  
  // Add Attendance
  async function addAttendance(employee_id, date, status) {
    let datas = {
      employee_id,
      date,
      status,
    };

    let response = await addData("api/admin/addAttendance.php",datas);
    let modal = getBootStrapModal("markAttendanceInput");
    let icon = response.success ? "success" : "error";
    SwalPopup(Swal, response.message, icon);
    modal.hide();
    table.ajax.reload();
  }

  $("#addAttendanceBTN").click(async function () {
    let employee_id = getElementValue("employeeName1");
    let date = getElementValue("employeeDate1");
    let status = getElementValue("employeeStatus1");

    if (employee_id && date && status) {
      addAttendance(employee_id, date, status);
    } else {
      SwalPopup(Swal,"All Input Fields required","warning")
    }
  });
  populateName(1);

  // Edit Attendance
  async function editAttendance(data) {
    if (!data) {
      console.error("Empty Input!!");
    }

    let response = await updateData("api/admin/updateAttendance.php",data);
    let modal = getBootStrapModal("editAttendanceInput");
    let icon = response.success ? "success": "error";
    SwalPopup(Swal,response.message,icon);
    modal.hide();
    table.ajax.reload();
  }

  $("#saveChangesBTN").click(async function () {
    let id = $(this).attr("data-id");
    let date = getElementValue("employeeDate2");
    let status = getElementValue("employeeStatus2");

    if (id != "" && date != "" && status != "None") {
      const data = {
        id,
        date,
        status,
      };
      editAttendance(data);
    } else {
      SwalPopup(Swal,"Input is Empty","danger")
    }
  });

  $("#AttendanceTable").on("click", "#editAtt", async function () {
    await populateName();
    let id = $(this).attr("data-id");
    let emp_id = $(this).attr("data-employee-id");
    let rowData = table
      .rows()
      .data()
      .toArray()
      .find((item) => item.attendance_id === id);
    document.querySelector(
      `#employeeName2 option[value="${emp_id}"]`
    ).selected = true;
    document.querySelector(`#employeeName2`).disabled = true;
    document.querySelector("#employeeDate2").value = rowData.attendance_date;
    let status = rowData.attendance_status;
    document.querySelector(
      `#employeeStatus2 option[value="${status.toLowerCase()}"]`
    ).selected = true;
    $("#saveChangesBTN").attr("data-id", id);
  });

  // Delete Attendance
  $("#AttendanceTable").on("click", "#deleteAtt", async function () {
    let id = $(this).attr("data-id");

     deleteSwalPopup(Swal).then(async (result) => {
        if (result.isConfirmed) {
          let response = await deleteData(`api/admin/deleteAttendance.php?id=${id}`);
          let icon = response.success? "success": "error";
          SwalPopup(Swal,response.message,icon);
          table.ajax.reload();
        }
      });

  });


  $("#markAttendanceInput #close,#markAttendanceInput .btn-close").click(
    function () {
      clearForm(1);
    }
  );
  $("#editAttendanceInput #close,#editAttendanceInput .btn-close").click(
    function () {
      clearForm();
    }
  );

  function filterByStatus(status) {

  }

  let filterSelect = document.querySelector("#attendanceFilter")

  filterSelect.addEventListener("change",function (e) {
    console.log(e.target.value);
  })

  // Reload Table
  $("#reset").click(function () {
    table.ajax.reload(null, false);
  });
});
