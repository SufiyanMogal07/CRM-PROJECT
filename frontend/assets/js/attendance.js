$(document).ready(function () {
  const authToken = localStorage.getItem("authToken");
  const decodedToken = jwt_decode(authToken);
  const table = $("#AttendanceTable").DataTable({
    ajax: {
      url: "http://localhost/CRM%20PROJECT/backend/api/admin/getAllAttendance.php",
      type: "GET",
      headers: {
        Authorization: `Bearer ${authToken}`,
        "Content-Type": "application/json",
      },
      dataSrc: function (json) {
        console.log(json.data);
        return json.data || [];
      },
      error: function (xhr, error, thrown) {
        $("#UserTable").html(
          `<tr><td colspan="8" class="text-center text-danger">Server error or no response from server. Please try again later.</td></tr>`
        );
      },
    },
    columns: [
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
            color = "secondary";
          }
          return `<span class="badge rounded-pill px-3 py-2 text-bg-${color}">${status.toUpperCase()}</span>`;
        },
      },
      {
        data: null,
        title: "Actions",
        render: function (data, type, row) {
          if (decodedToken.data.role === "admin") {
            return `<button id="editAtt" data-id="${row.attendance_id}" data-employee-id=${row.employee_id} class="edit-btn btn btn-success btn-sm admin-section" data-bs-toggle="modal" data-bs-target="#editAttendanceInput"><i class="fa-solid fa-pen-to-square"></i></button>
                        <button id="deleteAtt" data-id="${row.attendance_id}" data-employee-id=${row.employee_id} class="delete-btn btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>`;
          } else {
            return `<span class="text-muted">No Actions Availble</span>`;
          }
        },
      },
    ],
    responsive: true,
    pagination: true,
  });

  async function fetchEmployee() {
    try {
      let employees = await fetch(
        "http://localhost/CRM%20PROJECT/backend/api/admin/getAllEmployee.php",
        {
          method: "GET",
          headers: {
            Authorization: `Bearer ${authToken}`,
            "Content-type": "application/json",
          },
        }
      );
      let response = await employees.json();
      return response.data;
    } catch (error) {
      console.error(error);
    }
  }
  async function populateName(n = 2) {
    let employee = await fetchEmployee();
    const employeename = document.querySelector(`#employeeName${n}`);
    employeename.innerHTML += employee.map((item) => {
      return `<option value="${item.id}">${item.name}</option>`;
    });
  }
  
  function clearForm(n = 2) {
    $(`#employeeName${n}`).val("None");
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
    console.log(datas);
    try {
      let response = await fetch(
        "http://localhost/CRM%20PROJECT/backend/api/admin/addAttendance.php",
        {
          method: "POST",
          headers: {
            Authorization: `Bearer ${authToken}`,
            "Content-type": "application/json",
          },
          body: JSON.stringify(datas),
        }
      );
      let data = await response.json();

      if (data.success) {
        Swal.fire({
          title: data.message,
          icon: "success",
        });
        const modal = bootstrap.Modal.getInstance(
          document.getElementById("markAttendanceInput")
        );
        modal.hide();
        table.ajax.reload();
      } else {
        Swal.fire({
          title: data.message,
          icon: "error",
        });
      }
    } catch (error) {
      console.error(error);
    }
  }
  $("#addAttendanceBTN").click(async function () {
    let employee_id = $("#employeeName1").val();
    let date = $("#employeeDate1").val();
    let status = $("#employeeStatus1").val();
    if (employee_id && date && status) {
      addAttendance(employee_id, date, status);
    } else {
      Swal.fire({
        title: "All Input Fields required",
        icon: "warning",
      });
    }
  });
  populateName(1);

  // Edit Attendance
  async function editAttendance(data) {
    if (!data) {
      console.error("Empty Input!!");
    }

    let response = await fetch(
      "http://localhost/CRM%20PROJECT/backend/api/admin/updateAttendance.php",
      {
        method: "PATCH",
        headers: {
          Authorization: `Bearer ${authToken}`,
          "Content-type": "application/json",
        },
        body: JSON.stringify(data),
      }
    );
    response = await response.json();
    const modal = bootstrap.Modal.getInstance(
      document.getElementById("editAttendanceInput")
    );
    if (response.success) {
      table.ajax.reload();
      Swal.fire({
        title: response.message,
        icon: "success",
      });
    } else {
      Swal.fire({
        title: response.message,
        icon: "info",
      });
    }
    modal.hide();
  }

  $("#saveChangesBTN").click(async function () {
    let id = $(this).attr("data-id");
    let date = document.querySelector("#employeeDate2").value;
    let status = document.querySelector("#employeeStatus2").value;

    if (id != "" && date != "" && status != "None") {
      const data = {
        id,
        date,
        status,
      };
      editAttendance(data);
    } else {
      Swal.fire({
        title: "Input is Empty",
        icon: "gotdanger",
      });
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
    let response = await fetch(
      `http://localhost/CRM%20PROJECT/backend/api/admin/deleteAttendance.php/?id=${id}`,
      {
        method: "DELETE",
        headers: {
            Authorization: `Bearer ${authToken}`,
            'Content-type': 'application/json'
        }
      }
    );
    let data = await response.json();
    if (data.success) {
        table.ajax.reload();
        Swal.fire({
          title: data.message,
          icon: "success",
        });
      } else {
        Swal.fire({
          title: data.message,
          icon: "info",
        });
      }
  });

  $("#markAttendanceInput #close,#markAttendanceInput .btn-close").click(
    function () {
      clearForm(1);
    }
  );
  $("#editAttendanceInput #close,#markAttendanceInput .btn-close").click(
    function () {
      clearForm();
    }
  );
  
   // Reload Table
   $("#reset").click(function () {
    table.ajax.reload(null, false);
  });
});
