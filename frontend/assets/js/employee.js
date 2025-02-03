import {API_URL} from '../../config.js'
$(document).ready(function () {
  const authToken = localStorage.getItem("authToken");
  // Employee Table Load
  const table = $("#EmployeeTable").DataTable({
    ajax: {
      url: `${API_URL}api/admin/getAllEmployee.php`,
      type: "GET",
      headers: {
        Authorization: `Bearer ${authToken}`,
        "Content-type": "application/json",
      },
      dataSrc: function (json) {
        if (json.data && json.data.length === 0) {
          return [];
        }
        return json.data || [];
      },
      error: function (xhr, error, thrown) {
        $("#EmployeeTable").html(
          `<tr><td colspan="8" class="text-center text-danger fs-5">Server error or no response from server. Please try again later.</td></tr>`
        );
      },
    },
    columns: [
      { data: null,
        render: function(data, type, row, meta) {
            return meta.row + 1;
        }
     },
      { data: "name" },
      { data: "email" },
      { data: "phone" },
      {
        data: null,
        title: "Actions",
        render: function (data, type, row) {
          return `<button id="editEmployee" data-id="${row.id}" class="edit-btn btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#editEmployeeInput"><i class="fa-solid fa-pen-to-square"></i></button>
         <button id="deleteEmployee" data-id="${row.id}" class="delete-btn btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>`;
        },
      },
    ],
    searching: true,
    responsive: true,
  });

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
  function addEmployee(name, email, phone, pass) {
    const data = {
      name: name,
      email: email,
      phone: phone,
      password: pass,
    };
    fetch(`${API_URL}api/admin/addEmployee.php`, {
      method: "POST",
      headers: {
        Authorization: `Bearer ${authToken}`,
        "Content-type": "application/json",
      },
      body: JSON.stringify(data),
    })
      .then((response) => {
        if (!response.ok) {
          throw new Error("Server is not repsonding!!");
        }
        return response.json();
      })
      .then((data) => {
        const modal = bootstrap.Modal.getInstance(
          document.getElementById("addEmployeeInput")
        );
        if (data.success) {
          modal.hide();
          table.ajax.reload(null, false);
          Swal.fire({
            title: data.message,
            icon: "success",
          });
        }
        else {
          modal.hide();
          Swal.fire({
            title: data.message,
            icon: "error"
          });
        }
      })
      .catch((error) => {
        console.log(error);
      });
  }
  $("#addEmployeeBTN").click(function () {
    let name = document.querySelector("#employeeName1").value;
    let email = document.querySelector("#employeeEmail1").value;
    let phone = document.querySelector("#employeePhone1").value;
    let password = document.querySelector("#employeePassword1").value;

    if (name != "" && email != "" && phone != "" && password != "") {
      if (validateEmail(email)) {
        addEmployee(name, email, phone, password);
      } else {
        Swal.fire({
          title: "Email is not in valid format",
          icon: "error",
        });
      }
    } else {
      Swal.fire({
        title: "All Input Fields required!!!",
        icon: "error",
      });
    }
  });
  $("#addEmployeeInput .btn-close,#addEmployeeInput #close").click(() =>
    clearForm(1)
  );

  // Edit Employee
  function updateEmployee({ ...data }) {
    console.log(data);
    fetch(
      `${API_URL}api/admin/updateEmployee.php`,
      {
        method: "PATCH",
        headers: {
          Authorization: `Bearer ${authToken}`,
          "Content-Type": "application/json",
        },
        body: JSON.stringify(data),
      }
    )
      .then((response) => {
        if (!response.ok) {
          throw new Error("Server is Not Responding");
        }
        return response.json();
      })
      .then((data) => {
        const modal = bootstrap.Modal.getInstance(
          document.querySelector("#editEmployeeInput")
        );
        modal.hide();
        if (data.success) {
          Swal.fire({
            title: data.message,
            icon: "info",
          });
          table.ajax.reload();
        } else {
          Swal.fire({
            title: data.message,
            icon: "info",
          });
        }
      })
      .catch((error) => console.error(error));
  }
  $("#saveChangesBTN").click(function () {
    const id = $(this).attr("data-id");
    const name = $("#employeeName2").val();
    const email = $("#employeeEmail2").val();
    const phone = $("#employeePhone2").val();

    if (name != "" && email != "" && phone != "") {
      if (validateEmail(email)) {
        updateEmployee({ id, name, email, phone });
      } else {
        Swal.fire({
          title: "Email is not in valid format",
          icon: "error",
        });
      }
    } else {
      Swal.fire({
        title: "All Input fields Required!!",
        icon: "error",
      });
    }
  });
  $("#EmployeeTable").on("click", "#editEmployee", function () {
    const id = $(this).attr("data-id");
    const rowData = table
      .rows()
      .data()
      .toArray()
      .find((item) => item.id === id);
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
  function deleteEmployee(id) {
    if (!id) {
      console.error("Id is not passed");
      return;
    }
    fetch(
      `${API_URL}api/admin/deleteEmployee.php/?id=${id}`,
      {
        method: "DELETE",
        headers: {
          Authorization: `Bearer ${authToken}`,
          "Content-type": "application/json",
        },
      }
    )
      .then((response) => {
        return response.json();
      })
      .then((data) => {
        if (data.success) {
          table.ajax.reload();
          Swal.fire({
            title: data.message,
            icon: "success",
          });
        } else {
          Swal.fire({
            title: data.message,
            icon: "error",
          });
        }
      });
  }
  $("#EmployeeTable").on("click", "#deleteEmployee", function () {
    Swal.fire({
      title: "Are you sure?",
      text: "You won't be able to revert this!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, delete it!"
    }).then((result) => {
      if(result.isConfirmed) {
        let id = $(this).attr("data-id");
        deleteEmployee(id);
      }
    })
  });
  
  // Reload Table
  $("#reset").click(function () {
    table.ajax.reload(null, false);
  });
});
