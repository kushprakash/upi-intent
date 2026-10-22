<?php
include "header.php";
date_default_timezone_set('Asia/Kolkata');

// ---- Security Check (Only Admin) ----
if (!isset($conn)) { include "pages/dbInfo.php"; }
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// User Check
if(!isset($userdata) && isset($_SESSION['user_id'])) {
    $uid = $_SESSION['user_id'];
    $u_res = mysqli_query($conn, "SELECT * FROM users WHERE id='$uid'");
    $userdata = mysqli_fetch_assoc($u_res);
}

// Role Check
if (!isset($userdata['role']) || $userdata['role'] !== 'Admin') {
    echo "<script>window.location.href='dashboard';</script>";
    exit;
}

// ---- 1. Handle Update Plan (Edit Form) ----
if (isset($_POST['update_plan'])) {
    $id = intval($_POST['plan_db_id']);
    $name = $conn->real_escape_string($_POST['name']);
    $price = floatval($_POST['price']);
    $days = intval($_POST['duration_days']);
    $text = $conn->real_escape_string($_POST['duration_text']);

    $updateSql = "UPDATE subscription_plans SET 
                  name='$name', price='$price', duration_days='$days', duration_text='$text' 
                  WHERE id='$id'";

    if ($conn->query($updateSql)) {
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function(){
                Swal.fire({ 
                    title: "Updated!", text: "Plan updated successfully.", icon: "success",
                    confirmButtonColor: "#4e73df"
                }).then(()=>{ window.location.href="subscriptionplan.php"; });
            });
        </script>';
    } else {
        echo '<script>alert("Error updating: '.$conn->error.'");</script>';
    }
}

// ---- 2. Handle Toggle Actions (Active/GST) ----
if (isset($_POST['toggle_action'])) {
    $id = intval($_POST['id']);
    $col = $_POST['column']; 
    $current = intval($_POST['current_val']);
    $new_val = ($current == 1) ? 0 : 1;

    if(in_array($col, ['is_active', 'gst_enabled'])) {
        $conn->query("UPDATE subscription_plans SET $col='$new_val' WHERE id='$id'");
        echo "<script>window.location.href='subscriptionplan.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Manage Plans</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
  :root{
    --bg: #f8f9fc;
    --text: #5a5c69;
    --heading: #4e73df;
    --card-bg: #ffffff;
    --border: #e3e6f0;
    --primary: #4e73df;
    --success: #1cc88a; 
    --danger: #e74a3b;
    --shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
  }
  body{
    background-color: var(--bg);
    color: var(--text);
    font-family: 'Nunito', 'Segoe UI', sans-serif;
  }
  .app-content { min-height: 80vh; padding-top: 20px; }
  .container-max{ max-width:1200px; margin:0 auto; padding: 20px; }

  /* Header */
  .app-title {
    background: var(--card-bg);
    padding: 20px; border-radius: 10px;
    box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15);
    margin-bottom: 25px;
    display: flex; justify-content: space-between; align-items: center;
    border-left: 5px solid var(--primary);
  }
  .pg-title {
    font-size: 24px; font-weight: 700; color: #2e3440; margin: 0;
  }
  .breadcrumbs { font-size: 13px; color: #858796; }
  .breadcrumbs a { color: var(--primary); text-decoration: none; }

  /* Table Card */
  .clean-card {
    background: var(--card-bg);
    border-radius: 10px;
    box-shadow: var(--shadow);
    overflow: hidden;
    padding: 20px;
    border: 1px solid var(--border);
  }

  /* Table Styling */
  .table-responsive { overflow-x: auto; }
  table { width: 100%; border-collapse: collapse; color: #444; }
  
  th {
    text-align: left; padding: 15px;
    background: #f8f9fc;
    color: var(--primary); font-weight: 700; text-transform: uppercase; font-size: 12px;
    border-bottom: 2px solid var(--border);
  }
  td {
    padding: 15px; border-bottom: 1px solid var(--border);
    font-size: 14px; vertical-align: middle;
  }
  tr:hover { background: #fdfdfe; }

  /* Badges & Buttons */
  .badge-btn {
    padding: 6px 12px; border-radius: 50px; font-size: 11px; font-weight: 800;
    border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 5px;
    transition: all 0.2s; text-transform: uppercase;
  }
  .badge-success { background: #e6fffa; color: #1cc88a; border: 1px solid #1cc88a; }
  .badge-danger { background: #fff5f5; color: #e74a3b; border: 1px solid #e74a3b; }
  .badge-btn:hover { transform: scale(1.05); }

  .btn-edit {
    background: var(--primary); color: white; border: none; 
    padding: 8px 15px; border-radius: 5px;
    font-weight: 600; cursor: pointer; box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  }
  .btn-edit:hover { background: #2e59d9; transform: translateY(-1px); }

  /* Modal Styling (Light) */
  .modal-content {
    background: #fff; border-radius: 10px; border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
  }
  .modal-header { border-bottom: 1px solid #e3e6f0; background: #f8f9fc; padding: 15px 20px; border-radius: 10px 10px 0 0; }
  .modal-title { font-weight: 700; color: #4e73df; margin: 0; }
  .close { color: #858796; background: transparent; border: none; font-size: 24px; cursor:pointer;}
  .close:hover { color: #333; }
  
  .modal-body { padding: 25px; }
  .modal-footer { padding: 15px 20px; background: #f8f9fc; border-top: 1px solid #e3e6f0; border-radius: 0 0 10px 10px; }

  .form-control {
    background: #fff; border: 1px solid #d1d3e2;
    color: #6e707e; border-radius: 5px; padding: 10px; width: 100%;
    font-size: 14px;
  }
  .form-control:focus {
    border-color: #bac8f3; outline: none; box-shadow: 0 0 0 0.2rem rgba(78,115,223,.25);
  }
  label { color: #5a5c69; font-weight: 700; font-size: 13px; margin-bottom: 5px; display: block; }

</style>
</head>
<body>

<main class="app-content">
  <div class="container-max">
    
    <div class="app-title">
        <div>
            <h1 class="pg-title"><i class="fa fa-tasks"></i> Manage Plans</h1>
            <div class="breadcrumbs"><a href="dashboard">Dashboard</a> &nbsp;/&nbsp; Subscription Manager</div>
        </div>
    </div>

    <div class="clean-card">
      <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Plan Name</th>
                    <th>Price</th>
                    <th>Duration</th>
                    <th>GST Status</th>
                    <th>Plan Status</th>
                    <th style="text-align:right;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM subscription_plans ORDER BY id ASC";
                $res = $conn->query($sql);

                if ($res->num_rows > 0) {
                    while($row = $res->fetch_assoc()) {
                ?>
                <tr>
                    <td style="color:#858796;">#<?php echo $row['plan_id']; ?></td>
                    <td style="font-weight:700; color:#4e73df;"><?php echo htmlspecialchars($row['name']); ?></td>
                    <td style="font-weight:700; color:#1cc88a;">₹<?php echo number_format($row['price'], 2); ?></td>
                    <td>
                        <span style="font-weight:600;"><?php echo $row['duration_days']; ?> Days</span><br>
                        <span style="font-size:12px; color:#858796;"><?php echo $row['duration_text']; ?></span>
                    </td>
                    
                    <td>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="column" value="gst_enabled">
                            <input type="hidden" name="current_val" value="<?php echo $row['gst_enabled']; ?>">
                            <input type="hidden" name="toggle_action" value="1">
                            <button class="badge-btn <?php echo ($row['gst_enabled']==1)?'badge-success':'badge-danger'; ?>">
                                <i class="fa <?php echo ($row['gst_enabled']==1)?'fa-check':'fa-times'; ?>"></i>
                                <?php echo ($row['gst_enabled']==1)?'Enabled':'Disabled'; ?>
                            </button>
                        </form>
                    </td>

                    <td>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="hidden" name="column" value="is_active">
                            <input type="hidden" name="current_val" value="<?php echo $row['is_active']; ?>">
                            <input type="hidden" name="toggle_action" value="1">
                            <button class="badge-btn <?php echo ($row['is_active']==1)?'badge-success':'badge-danger'; ?>">
                                <i class="fa <?php echo ($row['is_active']==1)?'fa-eye':'fa-eye-slash'; ?>"></i>
                                <?php echo ($row['is_active']==1)?'Active':'Hidden'; ?>
                            </button>
                        </form>
                    </td>

                    <td style="text-align:right;">
                        <button class="btn-edit edit-btn" 
                            data-id="<?php echo $row['id']; ?>"
                            data-name="<?php echo $row['name']; ?>"
                            data-price="<?php echo $row['price']; ?>"
                            data-days="<?php echo $row['duration_days']; ?>"
                            data-text="<?php echo $row['duration_text']; ?>"
                            data-toggle="modal" data-target="#editModal">
                            <i class="fa fa-pencil"></i> Edit
                        </button>
                    </td>
                </tr>
                <?php 
                    }
                } else {
                    echo "<tr><td colspan='7' style='text-align:center; padding:30px; color:#858796;'>No plans found in database.</td></tr>";
                }
                ?>
            </tbody>
        </table>
      </div>
    </div>

  </div>
</main>

<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa fa-edit"></i> Edit Plan Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="plan_db_id" id="edit_id">
                    
                    <div class="form-group mb-3">
                        <label>Plan Name</label>
                        <input type="text" class="form-control" name="name" id="edit_name" required>
                    </div>

                    <div class="form-group mb-3">
                        <label>Price (₹)</label>
                        <input type="number" step="0.01" class="form-control" name="price" id="edit_price" required>
                    </div>

                    <div class="row">
                        <div class="col-6 mb-3">
                            <label>Duration (Days)</label>
                            <input type="number" class="form-control" name="duration_days" id="edit_days" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label>Display Text</label>
                            <input type="text" class="form-control" name="duration_text" id="edit_text" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" style="border:none; padding:8px 16px;" data-dismiss="modal">Close</button>
                    <button type="submit" name="update_plan" class="btn btn-primary" style="background:#4e73df; border:none; padding:8px 16px;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="js/jquery-3.2.1.min.js"></script>
<script src="js/popper.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>

<script>
    // Pass Data to Modal
    $(document).on('click', '.edit-btn', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        var price = $(this).data('price');
        var days = $(this).data('days');
        var text = $(this).data('text');

        $('#edit_id').val(id);
        $('#edit_name').val(name);
        $('#edit_price').val(price);
        $('#edit_days').val(days);
        $('#edit_text').val(text);
        
        // Open Modal manually if bootstrap data-target fails
        $('#editModal').modal('show');
    });
</script>

</body>
</html>