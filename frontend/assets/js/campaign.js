import { initDataTable } from "./helper/DataTableHelper.js";
import { addData, updateData, authToken, deleteData } from "./helper/apiClient.js";
import { getActionbuttons, SwalPopup, getBootStrapModal, deleteSwalPopup } from "./helper/uiHelper.js";

let table;
$(document).ready(async function () {

  const decodedToken = jwt_decode(authToken);
  let url = "api/campaigns/";

  let role = decodedToken.data.role;

  if (role === "employee") {
    document.getElementById("addCampaign").disabled = true;
  }
  // Campaign Table Load
  table = initDataTable("#CampaignTable", `${url}getAllCampaign.php`,
    [
      {
        data: null,
        title: "id",
        render: function (data, type, row, meta) {
          return meta.row + 1;
        }
      },
      { data: "campaign_name" },
      { data: "description" },
      {
        data: null,
        title: "Actions",
        render: function (data, type, row) {
          if (decodedToken.data.role === "admin") {
            return getActionbuttons("editCampaign", "editCampaignInput", "deleteCampaign", row.id);
          }
          else {
            return `<span class="text-muted">No Actions Availble</span>`
          }
        },
      }
    ], "Server error or no response from server. Please try again later."
  );

  function clearForm(n = 1) {
    $(`#campaignName${n}`).val("");
    $(`#campaignDescription${n}`).val("");
  }

  // Add Campaign
  async function addCampaign(name, CampaignDescription) {
    let data = await addData(`${url}addCampaign.php`, { name, CampaignDescription });
    let modal = getBootStrapModal("addCampaignInput");
    modal.hide();
    if (data.success) {
      SwalPopup(Swal, data.message, "success");
    } else {
      SwalPopup(Swal, data.message, "error");
    }
    clearForm();
    table.ajax.reload(null, false);
  }

  $("#addCampaignBTN").click(function () {
    let name = document.querySelector("#campaignName1").value;
    let desc = document.querySelector("#campaignDescription1").value;
    if (name && desc) {
      addCampaign(name, desc);
    } else {
      SwalPopup(Swal, "All Input Fields required!!", "error");
    }
  });


  // Update Campaign 
  async function updateCampaign(id, campaign_name, description) {
    let data = await updateData(`${url}updateCampaign.php`, {
      id, campaign_name, description
    })
    let modal = getBootStrapModal("editCampaignInput");

    if (!data.success) {
      SwalPopup(Swal, data.message, "error");
    } else {
      SwalPopup(Swal, data.message, "success");
    }
    clearForm(2);
    modal.hide();
    table.ajax.reload(null, false);
  }

  $("#saveChangesBTN").click(function () {
    const id = $(this).attr("data-id");
    const campaign_name = $("#campaignName2").val();
    const description = $("#campaignDescription2").val();
    let modal = getBootStrapModal("editCampaignInput");

    if (!id || !campaign_name || !description) {
      SwalPopup(Swal, "All Input Fields Required", "error");
      modal.hide();
    }
    updateCampaign(id, campaign_name, description);
  })

  $("#CampaignTable").on("click", "#editCampaign", function () {
    let id = $(this).attr("data-id");
    const rowData = table.rows().data().toArray().find((item) => item.id == id);

    $("#saveChangesBTN").attr("data-id", rowData.id);
    if (rowData) {
      $("#campaignName2").val(rowData.campaign_name);
      $("#campaignDescription2").val(rowData.description);
    }
  })

  // Delete Campaign
  async function deleteCampaign(id) {
    if (id) {
      let data = await deleteData(`${url}deleteCampaign.php?id=${id}`);
      let icon = data.success ? "success" : "error";
      SwalPopup(Swal, data.message, icon);
      table.ajax.reload(null, false);
    } else {
      console.error("Id is Missing!!!")
    }
  }
  $("#CampaignTable").on("click", "#deleteCampaign", function () {
    deleteSwalPopup(Swal).then((result) => {
      if (result.isConfirmed) {
        let id = $(this).attr("data-id");
        deleteCampaign(id);
      }
    });
  })

  $("#addCampaignInput .btn-close,#addCampaignInput #close").click(() =>
    clearForm(1)
  );

  $("#editCampaignInput .btn-close,#editCampaignInput #close").click(() =>
    clearForm(2)
  );

  // Reload Table
  $("#reset").click(function () {
    table.ajax.reload(null, false);
  });

});
