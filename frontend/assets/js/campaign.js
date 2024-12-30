$(document).ready(function () {
    const authToken = localStorage.getItem("authToken");
    const decodedToken = jwt_decode(authToken);
    // User Table Load
    const table = $("#CampaignTable").DataTable({
      ajax: {
        url: "http://localhost/CRM%20PROJECT/backend/api/admin/getAllCampaign.php",
        type: "GET",
        headers: {
          Authorization: `Bearer ${authToken}`,
          "Content-Type": "application/json",
        },
        dataSrc: function (json) {
          if(!json.data) {
            console.log("empty")
          }
          return json.data || [];
        },
        error: function (xhr, error, thrown) {
          $("#CampaignTable").html(
            `<tr><td colspan="8" class="text-center text-danger">Server error or no response from server. Please try again later.</td></tr>`
          );
        },
      },
      columns: [
        { data: "id" },
        { data: "campaign_name" },
        { data: "description" },
        // {
        //   data: null,
        //   render: function (data, type, row) {
        //     if(decodedToken.data.role==="admin") {
        //       return `<button id="editCampaign" data-id="${row.id}" class="edit-btn btn btn-success btn-sm admin-section" data-bs-toggle="modal" data-bs-target="#editCampaignInput"><i class="fa-solid fa-pen-to-square"></i></button>
        //       <button id="deleteCampaign" data-id="${row.id}" class="delete-btn btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>`;
        //     }
        //     else {
        //       return `<span class="text-muted">No Actions Availble</span>`
        //     }
        //   },
        // },
      ],
      searching: true,
      responsive: true,
    });
    function clearForm(n = 2) {
      $(`#CampaignName${n}`).val("");
      $(`#CampaignDescription${n}`).val("");
    }
    // Add Campaign
    function addCampaign(name,CampaignDescription) {
      const data = {
        name,
        CampaignDescription
      };
      fetch("http://localhost/CRM%20PROJECT/backend/api/admin/addCampaign.php",{
        method: "POST",
        headers: {
          'Authorization': `Bearer ${authToken}`,
          'Content-type': 'application/json'
        },
        body: JSON.stringify(data)
      }).then((response)=> {
        return response.json();
      }).then((data)=> {
        if(data.success) {
          console.log(data);
          const modal = bootstrap.Modal.getInstance(document.getElementById('addCampaignInput'));
          modal.hide();
          table.ajax.reload();
          Swal.fire({
            title: data.message,
            icon: "success"
          })
        }
      }).catch((error)=>{
        console.log(error)
      })
    }
    $("#addCampaignBTN").click(function () {
      let name = document.querySelector("#campaignName1").value;
      let desc = document.querySelector("#campaignDescription1").value;
      if (name && desc) {
        addCampaign(name,desc);
      } else {
        Swal.fire({
          title: "All Input Fields required!!!",
          icon: "error",
        });
      }
    });

     // Reload Table
  $("#reset").click(function () {
    table.ajax.reload(null, false);
  });
  });
  