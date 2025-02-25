import { getData, updateData } from "./helper/apiClient.js";
import { initDataTable } from "./helper/DataTableHelper.js";

let table
$(document).ready(function () {
  let url = "api/tasks/"

  table = initDataTable("#myTaskTable", `${url}getAllTask.php`, [
    {
      data: null,
      render: function (data, type, row, meta) {
        return meta.row + 1;
      },
    },
    { data: "campaignName" },
    { data: "userName" },
    {
      data: null,
      title: "Status",
      render: function (data, type, row) {
        let status = row.status || "Pending";
        let element = `<select data-id=${
          row.taskID
        } class="form-select-sm form-s">
                  <option value="Pending" ${
                    status === "Pending" ? "selected" : ""
                  }>Pending</option>
                  <option value="In Progress"  ${
                    status === "In Progress" ? "selected" : ""
                  }>In Progress</option>
                  <option value="Completed"  ${
                    status === "Completed" ? "selected" : ""
                  }>Completed</option>
                  </select>`;
        return element;
      },
    },
    { data: "action" },
  ]);

  // $("#myTaskTable").on("change",'.form-s',function () {
  //   let status = $(this).val();
  //   let id = $(this).attr('data-id');
  //   console.log(`Row with Id: ${id} and status: ${status}`);
  // })

  $(document).on("change", "select.form-s", function () {
    let id = $(this).attr('data-id');
    const $select = $(this);
    const status = $select.val();


    // Show the confirmation dialog
    Swal.fire({
      title: "Are you sure?",
      text: "Do you want to change the status?",
      icon: "warning",
      showCancelButton: true,
      confirmButtonText: "Yes, change it!",
      cancelButtonText: "No, keep current",
    }).then(async (result) => {
      if (result.isConfirmed) {
        
        let data = {id,status};
        let result = await updateData(`${url}updateTask.php`,data);
        console.log(result);
        if(result.success) {
          Swal.fire({
            title: "Task Status",
            text: result.message,
            icon: "success"
          });
          table.ajax.reload(null,false);
        } else {
          Swal.fire({
            title: "Task Status",
            text: result.message,
            icon: "error"
          })
          table.ajax.reload(null,false);
        }

      } else {
        table.ajax.reload(null,false);
      }
    });
  });

  // Reload Table
  $("#reset").click(function () {
    table.ajax.reload(null, false);
  });
});
