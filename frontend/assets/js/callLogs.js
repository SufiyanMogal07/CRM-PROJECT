import { initDataTable } from "./helper/DataTableHelper.js";
import {
  addData,
  updateData,
  authToken,
  deleteData,
  getData,
} from "./helper/apiClient.js";
import {
  getActionbuttons,
  SwalPopup,
  getBootStrapModal,
  deleteSwalPopup,
} from "./helper/uiHelper.js";

let table;
$(document).ready(function () {
  const decodedToken = jwt_decode(authToken);

  let columns = [
    {
      data: null,
      title: "#",
      render: function (data, type, row, meta) {
        return meta.row + 1;
      },
    },
    { data: "user_name" },
    { data: "call_time" },
    { data: "call_date" },
    { data: "outcome" },
    { data: "remarks" },
    {
      data: null,
      title: "conversion",
      render: function (data, type, row) {
        let value = "";
        if (row.conversion == 0) {
          value = "No";
        } else if (row.conversion === 1) {
          value = "Yes";
        }
        return `<span>${value}</span>`;
      },
    },
    {
      data: null,
      title: "Action",
      render: function (data, type, row) {
        let role = decodedToken.data.role;
        if (role === "admin" || role === "employee") {
          return getActionbuttons(
            "editCallLogs",
            "editCallLogsInput",
            "deleteCallLogs",
            row.id
          );
        }
      },
    },
  ];

  if (decodedToken.data.role === "admin") {
    // Insert at index 1 so that it appears after the row number column
    columns.splice(1, 0, { data: "employee_name", title: "Employee Name" });
  }

  table = initDataTable(
    "#CallLogsTable",
    "api/shared/getAllCallLogs.php",
    columns,
    "Server error or no response from server. Please try again later."
  );

  async function populateOptions(url, selector, dataName) {
    if (!url || !selector || !dataName) {
      console.error("Send Data is Empty");
      return;
    }
    let response = await getData(url);
    if (!response.success) {
      SwalPopup(data.message, "error");
    }
    let data = response.data;

    let element = document.querySelector(`#${selector}`);
    element = element ?? null;
    if (!element) {
      return console.error(
        "Selector Passed to Populate Options is Not Existed"
      );
    }
    element.innerHTML = "";
    element.innerHTML += `<option value="">Select</option>`;

    data.map(
      (elem) =>
        (element.innerHTML += `<option value="${elem.id}">${elem[dataName]}</option>`)
    );
  }

  function clearForm(n = 2) {
    if ($(`employeeName${n}`)) {
      $(`employeeName${n}`).val("");
    }
    $(`userName${n}`).val("");
    $(`callTime${n}`).val("");
    $(`callDate${n}`).val("");
    $(`outCome${n}`).val("");
    $(`remarks${n}`).val("");
    $(`conversion${n}`).val("");
  }

  async function addCallLogs(data) {
    let response = await addData("api/shared/addCallLogs.php", data);
    let icon = response.success ? "success" : "error";
    let modal = getBootStrapModal("addCallLogsInput");
    SwalPopup(Swal, response.message, icon);
    modal.hide();
    table.ajax.reload(null, false);
    clearForm(1);
  }

  $("#addCallLogsBTN").click(function () {
    let user_id = $("#userName1").val();
    let time = $("#callTime1").val();
    let date = $("#callDate1").val();
    let outcome = $("#outCome1").val();
    let remarks = $("#remarks1").val();
    let conversion = $("#conversion1").val();
    // let modal = getBootStrapModal("addCallLogsInput");

    if (!user_id || !time || !date || !outcome || !conversion) {
      return SwalPopup(Swal, "Input is Empty!!", "error");
    }
    let data = { user_id, time, date, outcome, remarks, conversion };

    if ($("#employeeName1").length && decodedToken.data.role === "admin") {
      let employee_id = $("#employeeName1").val();
      if (!employee_id) {
        return SwalPopup(Swal, "Employee is Not Selected!!", "error");
      }
      data.employee_id = employee_id;
    }
    addCallLogs(data);
  });

  $("#addCallLogs").click(async function () {
    populateOptions("api/shared/getAllUsers.php", "userName1", "name");
    let role = decodedToken.data.role;
    if (role === "admin") {
      populateOptions("api/admin/getAllEmployee.php", "employeeName1", "name");
    }
  });

  // Update Call Logs
  async function updateCallLogs(data) {
    let response = await updateData("api/shared/updateCallLogs.php", data);
    let modal = getBootStrapModal("editCallLogsInput");

    if (!response.success) {
      SwalPopup(Swal, response.message, "error");
    } else {
      SwalPopup(Swal, response.message, "success");
    }
    modal.hide();
    table.ajax.reload(null, false);
    clearForm(2);
  }

  $("#saveChangesBTN").click(async function () {
    let id = $(this).attr("data-id");
    let time = $("#callTime2").val();
    let date = $("#callDate2").val();
    let outcome = $("#outCome2").val();
    let remarks = $("#remarks2").val();
    let conversion = $("#conversion2").val();
    let modal = getBootStrapModal("editCallLogsInput");

    if (!id || !time || !date || !outcome || !conversion) {
      SwalPopup(Swal, "All Input Fields Required", "error");
      modal.hide();
    }
    let data = { id, time, date, outcome, remarks, conversion };

    if ($("#employeeName2").length && decodedToken.data.role === "admin") {
      let employee_id = $("#employeeName2").val();
      if (!employee_id) {
        return SwalPopup(Swal, "Employee is Not Selected!!", "error");
      }
      data.employee_id = employee_id;
    }
    updateCallLogs(data);
  });

  $("#CallLogsTable").on("click", "#editCallLogs", async function () {
    let id = $(this).attr("data-id");
    await populateOptions("api/shared/getAllUsers.php", "userName2", "name");
    let role = decodedToken.data.role;
    if (role === "admin") {
      await populateOptions("api/admin/getAllEmployee.php", "employeeName2", "name");
    }
    const rowData = table
      .rows()
      .data()
      .toArray()
      .find((item) => item.id == id);

    if ($("#employeeName2").length && decodedToken.data.role === "admin") {
      document.querySelector(
        `#employeeName2 option[value="${rowData.employee_id}"]`
      ).selected = true;
      $("#employeeName2").prop("disabled", true);
    }

    document.querySelector(
      `#userName2 option[value="${rowData.user_id}"]`
    ).selected = true;
    $("#userName2").prop("disabled",true);
    $("#callTime2").val(rowData.call_time);
    $("#callDate2").val(rowData.call_date);
    $("#outCome2").val(rowData.outcome);
    $("#remarks2").val(rowData.remarks);
    $("#conversion2").val(rowData.conversion);
    $("#saveChangesBTN").attr("data-id", rowData.id);
  });

  // Delete Call Logs
  async function deleteCallLogs(id) {
    if (id) {
      let data = await deleteData(`api/shared/deleteCallLogs.php?id=${id}`);
      let icon = data.success ? "success" : "error";
      SwalPopup(Swal, data.message, icon);
      table.ajax.reload(null, false);
    } else {
      console.error("Id is Missing!!!");
    }
  }
  $("#CallLogsTable").on("click", "#deleteCallLogs", function () {
    let id = $(this).attr("data-id");
    deleteSwalPopup(Swal).then((result) => {
      if (result.isConfirmed) {
        deleteCallLogs(id);
      }
    });
  });

  $("#addCallLogsInput .btn-close,#addCallLogsInput #close").click(() =>
    clearForm(1)
  );

  $("#editCallLogsInput .btn-close,#editCallLogsInput #close").click(() =>
    clearForm(2)
  );

  // Reload Table
  $("#reset").click(function () {
    table.ajax.reload(null, false);
  });
});
