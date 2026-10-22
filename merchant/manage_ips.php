<?php
include "header.php";
?>

<main class="app-content">
  <div class="app-title">
    <div>
      <h1><i class="fa fa-server"></i> Manage IPs</h1>
      <p>Whitelist your server IPs for secure API access</p>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="tile">
        <h3 class="tile-title">Add New IP</h3>
        <div class="tile-body">
          <form id="addIPForm">
            <input type="hidden" name="type" value="addIP">
            <div class="form-group">
              <label class="control-label">Sever IP Address</label>
              <input class="form-control" type="text" name="ip" placeholder="e.g. 192.168.1.1" required>
            </div>
            <button class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-check-circle"></i>Submit for Approval</button>
          </form>
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="tile">
        <h3 class="tile-title">Your Whitelisted IPs</h3>
        <div class="table-responsive">
          <table class="table table-hover table-bordered" id="ipTable">
            <thead>
              <tr>
                <th>ID</th>
                <th>IP Address</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $domains = $conn->query("SELECT * FROM merchant_ips WHERE user_token = '{$userdata['user_token']}'");
              if($domains->num_rows > 0){
                  while($row = $domains->fetch_assoc()){
                      $status_badge = ($row['status'] == 'Approved') ? 'badge-success' : (($row['status'] == 'Rejected') ? 'badge-danger' : 'badge-warning');
              ?>
              <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['ip_address']) ?></td>
                <td><span class="badge <?= $status_badge ?>"><?= $row['status'] ?></span></td>
                <td>
                    <button class="btn btn-danger btn-sm deleteIPBtn" data-id="<?= $row['id'] ?>"><i class="fa fa-trash"></i> Delete</button>
                </td>
              </tr>
              <?php }} else { ?>
              <tr><td colspan="4" class="text-center">No IPs added yet.</td></tr>
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
    $("#addIPForm").submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: 'backend/ip_actions.php',
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

    $(".deleteIPBtn").click(function() {
        if(!confirm('Are you sure?')) return;
        let id = $(this).data("id");
        $.ajax({
            url: 'backend/ip_actions.php',
            type: 'POST',
            data: {type: 'deleteIP', id: id},
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
