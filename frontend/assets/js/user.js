import { API_URL } from "../../config.js";
import { addData, authToken, decodedToken, deleteData, updateData } from "./helper/apiClient.js";
import { initDataTable } from "./helper/DataTableHelper.js";
import {
  getActionbuttons,
  getBootStrapModal,
  SwalPopup,
  getElementValue,
} from "./helper/uiHelper.js";

let table;
$(document).ready(function () {
  // User Table Load
  table = initDataTable("#UserTable", "api/shared/getAllUsers.php", [
    {
      data: null,
      render: function (data, type, row, meta) {
        return meta.row + 1;
      },
    },
    { data: "name" },
    { data: "phone" },
    { data: "email" },
    { data: "address" },
    { data: "city" },
    { data: "passportno" },
    {
      data: null,
      title: "Actions",
      render: function (data, type, row) {
        if (decodedToken(jwt_decode).data.role === "admin") {
          return getActionbuttons(
            "editUser",
            "editUserInput",
            "deleteUser",
            row.id
          );
        } else {
          return `<span class="text-muted">No Actions Availble</span>`;
        }
      },
    },
  ]);

  function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }
  function clearForm(n = 2) {
    $(`#userName${n}`).val("");
    $(`#userPhone${n}`).val("");
    $(`#userEmail${n}`).val("");
    $(`#userAddress${n}`).val("");
    $(`#userCity${n}`).val("");
    $(`#userPassport${n}`).val("");
  }
  // Add User
  async function addUser(name, phone, email, address, city, passport) {
    const data = {
      name,
      phone,
      email,
      address,
      city,
      passport,
    };
    let response = await addData("api/admin/addUsers.php", data);
    let modal = getBootStrapModal("addUserInput");
    let icon = response.success ? "success" : "error";
    SwalPopup(Swal, response.message, icon);
    modal.hide();
    table.ajax.reload();
    clearForm(1);
  }

  
  $("#addUserBTN").click(function () {
    let name = document.querySelector("#userName1").value;
    let phone = document.querySelector("#userPhone1").value;
    let email = document.querySelector("#userEmail1").value;
    let address = document.querySelector("#userAddress1").value;
    let city = document.querySelector("#userCity1").value;
    let passport = document.querySelector("#userPassport1").value;

    if (
      name != "" &&
      phone != "" &&
      email != "" &&
      address != "" &&
      city != "" &&
      passport != ""
    ) {
      if (validateEmail(email)) {
        addUser(name, phone, email, address, city, passport);
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
  // Edit User
  async function updateUser({ ...data }) {
    let response = await updateData("api/admin/updateUser.php", data);
    let modal = getBootStrapModal("editUserInput");
    let icon = response.success ? "success" : "error";
    SwalPopup(Swal, response.message, icon);
    modal.hide();
    table.ajax.reload();
    clearForm();
  }

  $("#saveChangesBTN").click(function () {
    const id = $(this).attr("data-id");
    const name = $("#userName2").val();
    const phone = $("#userPhone2").val();
    const email = $("#userEmail2").val();
    const address = $("#userAddress2").val();
    const city = $("#userCity2").val();
    const passportno = $("#userPassport2").val();

    if (
      id != "" &&
      name != "" &&
      phone != "" &&
      email != "" &&
      address != "" &&
      city != "" &&
      passportno != ""
    ) {
      if (validateEmail(email)) {
        updateUser({ id, name, phone, email, address, city, passportno });
      } else {
        SwalPopup(Swal,"Email is not in valid format","error");
      }
    } else {
      SwalPopup(Swal,"All Input fields Required!!","error");
    }
  });

  $("#UserTable").on("click", "#editUser", function () {
    const id = $(this).attr("data-id");
    const rowData = table
      .rows()
      .data()
      .toArray()
      .find((item) => item.id === id);

    if (rowData) {
      $("#userName2").val(rowData.name);
      $("#userPhone2").val(rowData.phone);
      $("#userEmail2").val(rowData.email);
      $("#userAddress2").val(rowData.address);
      $("#userCity2").val(rowData.city);
      $("#userPassport2").val(rowData.passportno);
      $("#saveChangesBTN").attr("data-id", id);
    }
  });

  $("#editUserInput .btn-close,#editUserInput #close").click(() => clearForm());

  async function deleteUser(id) {
    if (!id) {
      console.error("Id is not passed");
      return;
    }
    let response = await deleteData(`api/admin/deleteUser.php?id=${id}`);
    let icon = response.success ? "success" : "error";
    SwalPopup(Swal, response.message, icon);
    table.ajax.reload();
  }

  // Delete User
  $("#UserTable").on("click", "#deleteUser", function () {
    Swal.fire({
      title: "Are you sure?",
      text: "You won't be able to revert this!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, delete it!",
    }).then((result) => {
      if (result.isConfirmed) {
        let id = $(this).attr("data-id");
        deleteUser(id);
      }
    });
  });

  // Reload Table
  $("#reset").click(function () {
    table.ajax.reload(null, false);
  });
});
