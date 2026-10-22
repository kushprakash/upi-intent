<?php
include "header.php";
?>

<main class="app-content">
  <div class="app-title">
    <div>
      <h1><i class="fa fa-plug"></i> Manage Webhooks</h1>
      <p>Configure multiple webhook URLs for transaction updates</p>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="tile">
        <h3 class="tile-title">Add New Webhook</h3>
        <div class="tile-body">
          <form id="addWebhookForm">
            <input type="hidden" name="type" value="addWebhook">
            <div class="form-group">
              <label class="control-label">Webhook URL (e.g., https://example.com/callback)</label>
              <input class="form-control" type="url" name="url" placeholder="Enter your webhook URL" required>
            </div>
            <button class="btn btn-primary" type="submit"><i class="fa fa-fw fa-lg fa-plus"></i>Add Webhook</button>
          </form>
        </div>
      </div>
    </div>
    
    <div class="col-md-12">
      <div class="tile">
        <h3 class="tile-title">Your Webhooks</h3>
        <div class="table-responsive">
          <table class="table table-hover table-bordered">
            <thead>
              <tr>
                <th>ID</th>
                <th>Webhook URL</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              <?php
              $webhooks = $conn->query("SELECT * FROM merchant_webhooks WHERE user_token = '{$userdata['user_token']}'");
              if($webhooks->num_rows > 0){
                  while($row = $webhooks->fetch_assoc()){
              ?>
              <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['webhook_url']) ?></td>
                <td>
                    <button class="btn btn-danger btn-sm deleteWebhookBtn" data-id="<?= $row['id'] ?>"><i class="fa fa-trash"></i> Delete</button>
                </td>
              </tr>
              <?php }} else { ?>
              <tr><td colspan="3" class="text-center">No webhooks added yet.</td></tr>
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
    $("#addWebhookForm").submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: 'backend/webhook_actions.php',
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

    $(".deleteWebhookBtn").click(function() {
        if(!confirm('Are you sure?')) return;
        let id = $(this).data("id");
        $.ajax({
            url: 'backend/webhook_actions.php',
            type: 'POST',
            data: {type: 'deleteWebhook', id: id},
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
