$(document).ready(function () {
  const authToken = localStorage.getItem("authToken");
  const decodedToken = jwt_decode(authToken);
  // User Table Load
  const table = $("#UserTable").DataTable({
    ajax: {
      url: "http://localhost/CRM%20PROJECT/backend/api/shared/getAllUsers.php",
      type: "GET",
      headers: {
        Authorization: `Bearer ${authToken}`,
        "Content-Type": "application/json",
      },
      dataSrc: function (json) {
        return json.data || [];
      },
      error: function (xhr, error, thrown) {
        $("#UserTable").html(
          `<tr><td colspan="8" class="text-center text-danger">Server error or no response from server. Please try again later.</td></tr>`
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
      { data: "phone" },
      { data: "email" },
      { data: "address" },
      { data: "city" },
      { data: "passportno" },
      {
        data: null,
        title: "Actions",
        render: function (data, type, row) {
          if(decodedToken.data.role==="admin") {
            return `<button id="editUser" data-id="${row.id}" class="edit-btn btn btn-success btn-sm admin-section" data-bs-toggle="modal" data-bs-target="#editUserInput"><i class="fa-solid fa-pen-to-square"></i></button>
            <button id="deleteUser" data-id="${row.id}" class="delete-btn btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>`;
          }
          else {
            return `<span class="text-muted">No Actions Availble</span>`
          }
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
    $(`#userName${n}`).val("");
    $(`#userPhone${n}`).val("");
    $(`#userEmail${n}`).val("");
    $(`#userAddress${n}`).val("");
    $(`#userCity${n}`).val("");
    $(`#userPassport${n}`).val("");
  }
  // Add User
  function addUser(name, phone, email, address, city, passport) {
    const data = {
      name,
      phone,
      email,
      address,
      city,
      passport,
    };
    fetch("http://localhost/CRM%20PROJECT/backend/api/admin/addUsers.php",{
      method: "POST",
      headers: {
        'Authorization': `Bearer ${authToken}`,
        'Content-type': 'application/json'
      },
      body: JSON.stringify(data)
    }).then((response)=> {
      return response.json();
    }).then((data)=> {
      const modal = bootstrap.Modal.getInstance(document.getElementById('addUserInput'));
      if(data.success) {
        modal.hide();
        table.ajax.reload();
        Swal.fire({
          title: data.message,
          icon: "success"
        })
      } else {
        modal.hide();
        Swal.fire({
          title: data.message,
          icon: "error"
        });
      }
    }).catch((error)=>{
      console.log(error)
    })
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
  function updateUser({ ...data }) {
    console.log(data);
    fetch(
      "http://localhost/CRM%20PROJECT/backend/api/admin/updateUser.php",
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
          document.querySelector("#editUserInput")
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
    const name = $("#userName2").val();
    const phone = $("#userPhone2").val();
    const email = $("#userEmail2").val();
    const address = $("#userAddress2").val();
    const city = $("#userCity2").val();
    const passportno = $("#userPassport2").val();

    if (id != "" && name != "" && phone != "" && email != "" && address != "" && city!="" && passportno!="") {
      if (validateEmail(email)) {
        updateUser({ id, name, phone, email, address, city, passportno });
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
  $("#editUserInput .btn-close,#editUserInput #close").click(() =>
    clearForm()
  );
  function deleteUser(id) {
    if (!id) {
      console.error("Id is not passed");
      return;
    }
    fetch(`http://localhost/CRM%20PROJECT/backend/api/admin/deleteUser.php/?id=${id}`,
      {
        method: "DELETE",
        headers: {
          'Authorization': `Bearer ${authToken}`,
          'Content-type': 'application/json'
        }
      }
    ).then((response)=> {
      if(!response.ok) {
        throw new Error("Server is Not Responding");
      }
      return response.json();
    }).then((data) => {
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
  // Delete User
  $("#UserTable").on("click","#deleteUser",function () {
    Swal.fire({
      title: "Are you sure?",
      text: "You won't be able to revert this!",
      icon: "warning",
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Yes, delete it!"
    }).then((result) => {
      if (result.isConfirmed) {
        let id = $(this).attr('data-id');
        deleteUser(id);
      }
    });
  
  })
  // Reload Table
  $("#reset").click(function () {
    table.ajax.reload(null,false);
  });
});
