<?php
  $page = "Campaigns";
  $css = '';
  include("../layouts/dashboard_layout.php")
?>
<main class="main-body p-5 d-flex flex-column">
        <div class="d-flex justify-content-between align-items-center mb-5">
          <h2 class="">Manage Campaigns</h2>
          <div>
            <button id="reset" class="btn btn-danger">Reset</button>
            <button id="addCampaign" class="ms-2 btn btn-primary" data-bs-toggle="modal"
              data-bs-target="#addCampaignInput">Add Campaign</button>
          </div>
        </div>
        <div class="campaigns-container">
          <table id="CampaignTable" class="table table-striped w-100">
            <thead>
              <tr>
                <th>#</th>
                <th>Campaign Name</th>
                <th>Description</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
          </table>
          <!-- Add Campaign Modal -->
          <div class="modal fade" id="addCampaignInput" tabindex="-1" aria-labelledby="inputModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="inputModalLabel">Enter Campaign Details</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form id="campaignForm1">
                    <div class="mb-3">
                      <label for="campaignName1" class="form-label">Campaign Name</label>
                      <input type="text" class="form-control" id="campaignName1" placeholder="Enter Campaign Name"
                        required>
                    </div>
                    <div class="mb-3">
                      <label for="campaignDescription1" class="form-label">Campaign Description</label>
                      <textarea type="text" class="form-control" id="campaignDescription1"
                        placeholder="Enter Campaign Description" required maxlength="80"></textarea>
                    </div>
                  </form>
                </div>
                <div class="modal-footer">
                  <button id="close" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button id="addCampaignBTN" type="button" class="btn btn-primary">Add</button>
                </div>
              </div>
            </div>
          </div>

          <!-- Edit Campaign Modal -->
          <div class="modal fade" id="editCampaignInput" tabindex="-1" aria-labelledby="inputModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title" id="inputModalLabel">Edit Campaign Details</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form id="campaignForm2">
                    <div class="mb-3">
                      <label for="campaignName2" class="form-label">Campaign Name</label>
                      <input type="text" class="form-control" id="campaignName2" placeholder="Enter Campaign Name"
                        required>
                    </div>
                    <div class="mb-3">
                      <label for="campaignDescription2" class="form-label">Campaign Description</label>
                      <textarea type="text" class="form-control" id="campaignDescription2"
                        placeholder="Campaign Description" required maxlength="80"></textarea>
                    </div>
                  </form>
                </div>
                <div class="modal-footer">
                  <button id="close" type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                  <button data-id="1" id="saveChangesBTN" type="button" class="btn btn-primary">Save Changes</button>
                </div>
              </div>
            </div>
          </div>

          
        </div>
</main>
<?php
$script = "<script type='module' src='../assets/js/campaign.js'></script>";
include("../components/footer.php");
?>