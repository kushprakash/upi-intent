<?php
include "header.php";

if($userdata['role'] != 'Admin'){
    echo "<script>window.location.href='dashboard';</script>";
    exit;
}
?>

<main class="app-content">
  <div class="app-title">
    <div>
      <h1><i class="fa fa-shield"></i> Admin IP Approval</h1>
      <p>Approve or Reject merchant server IPs</p>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="tile">
        <h3 class="tile-title">Pending Requests</h3>
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>ID</th>
                <th>User Token</th>
                <th>IP Address</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $ips = $conn->query("SELECT * FROM merchant_ips WHERE status = 'Pending'");
              if($ips->num_rows > 0){
                  while($row = $ips->fetch_assoc()){
              ?>
              <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['user_token']) ?></td>
                <td><?= htmlspecialchars($row['ip_address']) ?></td>
                <td><span class="badge badge-warning"><?= $row['status'] ?></span></td>
                <td>
                    <button class="btn btn-success btn-sm updateStatusBtn" data-id="<?= $row['id'] ?>" data-status="Approved"><i class="fa fa-check"></i> Approve</button>
                    <button class="btn btn-danger btn-sm updateStatusBtn" data-id="<?= $row['id'] ?>" data-status="Rejected"><i class="fa fa-times"></i> Reject</button>
                </td>
              </tr>
              <?php }} else { ?>
              <tr><td colspan="5" class="text-center">No pending requests.</td></tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <div class="col-md-12">
        <div class="tile">
            <h3 class="tile-title">All IPs</h3>
             <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>ID</th>
                <th>User Token</th>
                <th>IP Address</th>
                <th>Status</th>
              </tr>
            </thead>
             <tbody>
              <?php
              $ips = $conn->query("SELECT * FROM merchant_ips ORDER BY id DESC LIMIT 50");
              if($ips->num_rows > 0){
                  while($row = $ips->fetch_assoc()){
                      $status_badge = ($row['status'] == 'Approved') ? 'badge-success' : (($row['status'] == 'Rejected') ? 'badge-danger' : 'badge-warning');
              ?>
              <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['user_token']) ?></td>
                <td><?= htmlspecialchars($row['ip_address']) ?></td>
                 <td><span class="badge <?= $status_badge ?>"><?= $row['status'] ?></span></td>
              </tr>
              <?php }} ?>
              </tbody>
              </table>
        </div>
    </div>

  </div>
</main>

<?php require_once 'footer.php'; ?>

<script>
$(document).ready(function() {
    $(".updateStatusBtn").click(function() {
        let id = $(this).data("id");
        let status = $(this).data("status");
        if(!confirm('Are you sure you want to ' + status + ' this IP?')) return;
        
        $.ajax({
            url: 'backend/ip_actions.php',
            type: 'POST',
            data: {type: 'updateStatus', id: id, status: status},
            success: function(response) {
                let res = JSON.parse(response);
                if(res.status == 200) {
                    popup('success', 'Updated', res.msg);
                } else {
                    popup_error('error', 'Error', res.msg);
                }
            }
        });
    });
});
</script>
