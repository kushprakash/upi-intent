<?php
include "header.php";
?>

<main class="app-content">
  <div class="app-title">
    <div>
      <h1><i class="fa fa-globe"></i> Manage Domains</h1>
      <p>Whitelist your domains for secure transactions</p>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="tile">
        <h3 class="tile-title">Add New Domain</h3>
        <div class="tile-body">
          <form id="addDomainForm">
            <input type="hidden" name="type" value="addDomain">
            <div class="form-group">
              <label class="control-label">Domain URL (e.g., https://example.com)</label>
              <input class="form-control" type="text" name="domain" placeholder="Enter your domain URL" required>
            </div>
            <button class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i>Submit for Approval</button>
          </form>
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="tile">
        <h3 class="tile-title">Your Whitelisted Domains</h3>
        <div class="table-responsive">
          <table class="table table-hover table-bordered" id="domainTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>Domain URL</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $domains = $conn->query("SELECT * FROM merchant_domains WHERE user_token = '{$userdata['user_token']}'");
              if($domains->num_rows > 0){
                  while($row = $domains->fetch_assoc()){
                      $status_badge = ($row['status'] == 'Approved') ? 'badge-success' : (($row['status'] == 'Rejected') ? 'badge-danger' : 'badge-warning');
              ?>
              <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['domain_url']) ?></td>
                <td><span class="badge <?= $status_badge ?>"><?= $row['status'] ?></span></td>
                <td>
                    <button class="btn btn-danger btn-sm deleteDomainBtn" data-id="<?= $row['id'] ?>"><i class="fa fa-trash"></i> Delete</button>
                </td>
              </tr>
              <?php }} else { ?>
              <tr><td colspan="4" class="text-center">No domains added yet.</td></tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</main>

<?php require_once 'footer.php'; ?>

<script>
$(document).ready(function() {
    $("#addDomainForm").submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: 'backend/domain_actions.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                let res = JSON.parse(response);
                if(res.status == 200) {
                    popup('success', 'Success', res.msg);
                } else {
                    popup_error('error', 'Error', res.msg);
                }
            }
        });
    });

    $(".deleteDomainBtn").click(function() {
        if(!confirm('Are you sure?')) return;
        let id = $(this).data("id");
        $.ajax({
            url: 'backend/domain_actions.php',
            type: 'POST',
            data: {type: 'deleteDomain', id: id},
            success: function(response) {
                let res = JSON.parse(response);
                if(res.status == 200) {
                    popup('success', 'Deleted', res.msg);
                } else {
                    popup_error('error', 'Error', res.msg);
                }
            }
        });
    });
});
</script>
