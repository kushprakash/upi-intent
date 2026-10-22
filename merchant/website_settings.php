<?php
include "header.php";

// Authorization check (Ensure only Admin role can access this within merchant panel)
// Assuming we want to keep the role check even if it's in merchant panel
if($userdata['role'] != 'Admin'){
    echo "<script>window.location.href='index';</script>";
    exit;
}
?>

<main class="app-content">
  <div class="app-title">
    <div>
      <h1><i class="fa fa-cogs"></i> Website Settings</h1>
      <p>Manage website configuration</p>
    </div>
  </div>

  <div class="row">
    <div class="col-md-12">
      <div class="tile">
        <div class="tile-body">
            <!-- Form points to merchant backend -->
          <form action="backend/update_website_settings.php" method="POST" enctype="multipart/form-data">
            
            <h4 class="mb-4 text-primary">General Settings</h4>
            <div class="row">
              <div class="col-md-6 form-group">
                <label class="control-label">Website Title</label>
                <input class="form-control" type="text" name="title" value="<?= htmlspecialchars($website_settings['title']) ?>" required>
              </div>
            </div>
            <div class="row">
              <div class="col-md-6 form-group">
                <label class="control-label">Current Logo</label><br>
                <!-- Path adjusted for merchant directory -->
                <img src="<?= $site_url ?>/<?= $website_settings['logo'] ?>" style="max-height: 50px;" class="mb-2">
                <input class="form-control" type="file" name="logo">
                <small class="text-muted">Upload to change. Allowed: png, jpg, jpeg, webp</small>
              </div>
              <div class="col-md-6 form-group">
                <label class="control-label">Current Favicon</label><br>
                <img src="<?= $site_url ?>/<?= $website_settings['favicon'] ?>" style="max-height: 32px;" class="mb-2">
                <input class="form-control" type="file" name="favicon">
                <small class="text-muted">Upload to change. Allowed: png, jpg, jpeg, ico</small>
              </div>
            </div>

            <hr>
            <h4 class="mb-4 text-primary">Contact Information</h4>
            <div class="row">
              <div class="col-md-6 form-group">
                <label class="control-label">Contact Email</label>
                <input class="form-control" type="email" name="contact_email" value="<?= htmlspecialchars($website_settings['contact_email']) ?>" required>
              </div>
              <div class="col-md-6 form-group">
                <label class="control-label">Contact Phone</label>
                <input class="form-control" type="text" name="contact_phone" value="<?= htmlspecialchars($website_settings['contact_phone']) ?>" required>
              </div>
            </div>
            <div class="form-group">
                <label class="control-label">Contact Address</label>
                <textarea class="form-control" name="contact_address" rows="3"><?= htmlspecialchars($website_settings['contact_address']) ?></textarea>
            </div>

            <hr>
            <h4 class="mb-4 text-primary">ReCaptcha Settings</h4>
            <div class="row">
              <div class="col-md-6 form-group">
                <label class="control-label">Site Key</label>
                <input class="form-control" type="text" name="recaptcha_site_key" value="<?= htmlspecialchars($website_settings['recaptcha_site_key']) ?>">
              </div>
              <div class="col-md-6 form-group">
                <label class="control-label">Secret Key</label>
                <input class="form-control" type="text" name="recaptcha_secret_key" value="<?= htmlspecialchars($website_settings['recaptcha_secret_key']) ?>">
              </div>
            </div>

            <hr>
            <h4 class="mb-4 text-primary">SMTP Configuration</h4>
             <div class="row">
              <div class="col-md-4 form-group">
                <label class="control-label">SMTP Host</label>
                <input class="form-control" type="text" name="smtp_host" value="<?= htmlspecialchars($website_settings['smtp_host']) ?>">
              </div>
              <div class="col-md-4 form-group">
                <label class="control-label">SMTP Username</label>
                <input class="form-control" type="text" name="smtp_username" value="<?= htmlspecialchars($website_settings['smtp_username']) ?>">
              </div>
               <div class="col-md-4 form-group">
                <label class="control-label">SMTP Password</label>
                <input class="form-control" type="password" name="smtp_password" value="<?= htmlspecialchars($website_settings['smtp_password']) ?>">
              </div>
            </div>
             <div class="row">
              <div class="col-md-3 form-group">
                <label class="control-label">SMTP Port</label>
                <input class="form-control" type="number" name="smtp_port" value="<?= htmlspecialchars($website_settings['smtp_port']) ?>">
              </div>
              <div class="col-md-3 form-group">
                <label class="control-label">Encryption</label>
                <select class="form-control" name="smtp_encryption">
                    <option value="tls" <?= $website_settings['smtp_encryption'] == 'tls' ? 'selected' : '' ?>>TLS</option>
                    <option value="ssl" <?= $website_settings['smtp_encryption'] == 'ssl' ? 'selected' : '' ?>>SSL</option>
                    <option value="" <?= $website_settings['smtp_encryption'] == '' ? 'selected' : '' ?>>None</option>
                </select>
              </div>
               <div class="col-md-3 form-group">
                <label class="control-label">From Email</label>
                <input class="form-control" type="email" name="smtp_from_email" value="<?= htmlspecialchars($website_settings['smtp_from_email']) ?>">
              </div>
               <div class="col-md-3 form-group">
                <label class="control-label">From Name</label>
                <input class="form-control" type="text" name="smtp_from_name" value="<?= htmlspecialchars($website_settings['smtp_from_name']) ?>">
              </div>
            </div>


            <div class="tile-footer">
              <button class="btn btn-primary" type="submit" name="update_settings"><i class="fa fa-fw fa-lg fa-check-circle"></i>Save Settings</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</main>

<?php require_once 'footer.php'; ?>
