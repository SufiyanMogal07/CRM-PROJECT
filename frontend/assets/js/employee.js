import { BASE_URL } from "../../config.js";
import { addData, deleteData, updateData, decodedToken } from "./helper/apiClient.js";
import { initDataTable } from "./helper/DataTableHelper.js";
import {
  getActionbuttons,
  getBootStrapModal,
  SwalPopup,
  getElementValue,
  deleteSwalPopup,
  
} from "./helper/uiHelper.js";

let table;
$(document).ready(function () {
  // Employee Table Initialize
  let token = decodedToken(jwt_decode);
  let role = token.data.role
  let crm =  `${BASE_URL}pages/CRMDashboard.php`
  const url = "api/employee/";

  if(role!=="admin") {
    window.location.href = crm;
  }
  
  table = initDataTable(
    "#EmployeeTable",
    `${url}getAllEmployee.php`,
    [
      {
        data: null,
        render: function (data, type, row, meta) {
          return meta.row + 1;
        },
      },
      { data: "name" },
      { data: "email" },
      { data: "phone" },
      {
        data: null,
        title: "Actions",
        render: function (data, type, row) {
          let role = decodedToken(jwt_decode).data.role;
          if(role === "admin") {
            return getActionbuttons(
              "editEmployee",
              "editEmployeeInput",
              "deleteEmployee",
              row.id,
            );
          } else {
            return `<span class="text-muted">No Actions Availble</span>`
          }
        },
      },
    ],
    "Server error or no response from server. Please try again later."
  );

  function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  function clearForm(n = 2) {
    $(`#employeeName${n}`).val("");
    $(`#employeeEmail${n}`).val("");
    $(`#employeePhone${n}`).val("");
    if (n == 1) {
      $(`#employeePassword${n}`).val("");
    }
  }

  // Add Employee
  async function addEmployee(name, email, phone, pass) {
    const data = {
      name: name,
      email: email,
      phone: phone,
      password: pass,
    };
    let employee = await addData(`${url}addEmployee.php`, data);
    let modal = getBootStrapModal("addEmployeeInput");
    let icon = employee.success ? "success" : "error";
    modal.hide();
    SwalPopup(Swal, employee.message, icon);
    clearForm(1);
    table.ajax.reload();
  }
  $("#addEmployeeBTN").click(function () {
    let name = getElementValue("employeeName1");
    let email = getElementValue("employeeEmail1");
    let phone = getElementValue("employeePhone1");
    let password = getElementValue("employeePassword1");
    let modal = getBootStrapModal("addEmployeeInput");

    if (name != "" && email != "" && phone != "" && password != "") {
      if (validateEmail(email)) {
          addEmployee(name, email, phone, password);
      } else {
          modal.hide();
          SwalPopup(Swal,"Email is not in valid format", "error");
      }
    } else {
      modal.hide();
      SwalPopup(Swal,"All Input Fields required!!!", "error");
    }
  });
  // Clear AddEmployee Form When Click on Close Button
  $("#addEmployeeInput .btn-close,#addEmployeeInput #close").click(() =>
    clearForm(1)
  );

  // Edit Employee
  async function updateEmployee({ ...data }) {
    let response = await updateData(`${url}updateEmployee.php`,data);
    let modal = getBootStrapModal("editEmployeeInput");
    let icon = response.success ? "success": "error";
    SwalPopup(Swal,response.message,icon);
    clearForm(2);
    modal.hide();
    table.ajax.reload();
  }

  $("#saveChangesBTN").click(function () {
    const id = $(this).attr("data-id");
    const name = $("#employeeName2").val();
    const email = $("#employeeEmail2").val();
    const phone = $("#employeePhone2").val();

    if (name !== "" && email !== "" && phone !== "") {
      if (validateEmail(email)) {
        updateEmployee({ id, name, email, phone });
      } else {
        SwalPopup(Swal,"Email is not in valid format", "error");
      }
    } else {
      SwalPopup(Swal,"All Input fields Required!!", "error");
    }
  });

  $("#EmployeeTable").on("click", "#editEmployee", function () {
    const id = $(this).attr("data-id");
    const rowData = table
      .rows()
      .data()
      .toArray()
      .find((item) => item.id == id);
    if (rowData) {
      $("#employeeName2").val(rowData.name);
      $("#employeeEmail2").val(rowData.email);
      $("#employeePhone2").val(rowData.phone);
      $("#saveChangesBTN").attr("data-id", id);
    }
  });
  $("#editEmployeeInput .btn-close,#editEmployeeInput #close").click(() =>
    clearForm()
  );

  // Delete Employee
  async function deleteEmployee(id) {
    if (!id) {
      console.error("Id is not passed");
      return;
    }
    let data = await deleteData(`${url}deleteEmployee.php?id=${id}`);
    let icon = data.success? "success": "error";

    SwalPopup(Swal,data.message,icon);
    table.ajax.reload();
  }

  $("#EmployeeTable").on("click", "#deleteEmployee",function () {
      deleteSwalPopup(Swal).then((result) => {
      if (result.isConfirmed) {
        let id = $(this).attr("data-id");
        deleteEmployee(id);
      }
    });
  });

  // Reload Table
  $("#reset").click(function () {
    table.ajax.reload(null, false);
  });
});
