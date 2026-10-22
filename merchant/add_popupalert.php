<?php include "header.php"; ?>

    
<?php

//   ini_set("display_errors",true);
//   error_reporting(E_ALL);
 
  
  if (isset($_POST['create'])) {
      
      $title = $_POST["title"];
      $image = $_FILES["image"];
      $imgname = $image["name"];
      $imgtmpname = $image["tmp_name"];
      $path = 'assets/img/alertimg/'.$imgname;
      
      $addnotif = $conn->query("INSERT INTO `popup_alert`(`title`, `img`) VALUES ('$title','$path')");
      
      if($addnotif){
          move_uploaded_file($imgtmpname,$path);
          
         echo '<script src="js/jquery-3.2.1.min.js"></script>';
         echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
        echo '<script>
        $("#loading_ajax").hide();
                        Swal.fire({
                            icon: "success",
                            title: "Success",
                            text: "Popup Alert Added Successfully.",
                            showConfirmButton: true,
                            confirmButtonText: "Ok",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "add_popupalert";
                            }
                        });
                    </script>';
                    exit;
      }else{
          echo '<script src="js/jquery-3.2.1.min.js"></script>';
         echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
        echo '<script>
        $("#loading_ajax").hide();
                        Swal.fire({
                            icon: "error",
                            title: "Error !",
                            text: "Failed to Add Alert !.",
                            showConfirmButton: true,
                            confirmButtonText: "Ok",
                        });
                    </script>';
                    exit;
      }
  
  }
  
  if (isset($_POST['delete'])) {
      
      $id = $_POST["srno"];
      $deletenotif = $conn->query("DELETE FROM `popup_alert` WHERE id = '$id'");
      
      if($deletenotif){
          
         echo '<script src="js/jquery-3.2.1.min.js"></script>';
         echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
        echo '<script>
        $("#loading_ajax").hide();
                        Swal.fire({
                            icon: "success",
                            title: "Success",
                            text: "Alert Deleted Successfully.",
                            showConfirmButton: true,
                            confirmButtonText: "Ok",
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = "add_popupalert";
                            }
                        });
                    </script>';
                    exit;
      }else{
          echo '<script src="js/jquery-3.2.1.min.js"></script>';
         echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18"></script>';
        echo '<script>
        $("#loading_ajax").hide();
                        Swal.fire({
                            icon: "error",
                            title: "Error !",
                            text: "Failed to Delete Alert !.",
                            showConfirmButton: true,
                            confirmButtonText: "Ok",
                        })
                    </script>';
                    exit;
      }
  
  }
  
  ?>
  
  

<main class="app-content">
      <div class="app-title">
        <div>
          <h1><i class="fa fa-user-plus"></i> Popup Alert</h1>
          <!-- <p>Start a beautiful journey here</p> -->
        </div>
        <ul class="app-breadcrumb breadcrumb">
          <li class="breadcrumb-item"><i class="fa fa-home fa-lg"></i></li>
          <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
        </ul>
      </div>
      
      <div class="tile mb-4">
        <div class="page-header">
          <div class="row">
            <div class="col-lg-12">
						<!-- <h4 class="page-title">UPI Settings</h4> -->
						<div class="row row-card-no-pd">
							<div class="col-md-12">

<?php if($userdata["role"] == 'Admin'){  ?>

                            <div class="main-panel">
					<div class="content">
					<div class="container-fluid">

						<h4 class="page-title">Create Popup Alert</h4>	
										
						<div class="row row-card-no-pd">							
							<div class="col-md-12">

 <form class="row mb-4" method="POST" enctype="multipart/form-data" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
    
      <div class="col-md-6 mb-2">
        <label>Title</label>
    <input type="text" name="title" placeholder="Title 10 words" class="form-control" required="" />
    </div>
      <div class="col-md-6 mb-2">
        <label>Image</label>
    <input type="file" name="image" class="form-control" required="" />
    </div>
    
    <div class="col-md-12 mb-2 mt-2"><button type="submit" name="create" class="btn btn-primary btn-sm">Submit</button>
    </div>

</form>


              </div>
            </div>
            
         <h4 class="page-title">List Of Popup Alert</h4>
						<div class="row row-card-no-pd">
							<div class="col-md-12">
								
							<div class="table-responsive">
								<table class="table table-sm table-hover table-bordered table-head-bg-primary" id="dataTable" width="100%">
										<thead>
											<tr>
												<th>#</th>
												<th>User</th>
												<th>Title</th>
												<th>Image</th>
												<th>Date</th>
												<th>Action</th>
												
											</tr>
										</thead>
										<tbody>
<?php
$query = "SELECT * FROM `popup_alert`";
$query_run = mysqli_query($conn, $query);

if ($query_run) {
    while ($row = mysqli_fetch_assoc($query_run)) {
     
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . "</td>";
        echo "<td>All</td>";
echo "<td>" . htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8') . "</td>";
echo "<td> <img src='" .$row['img']. "' width='100px' /></td>";
echo "<td>" . htmlspecialchars($row['date'], ENT_QUOTES, 'UTF-8') . "</td>";

     ?>
       <td>
      <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <input type="hidden" name="srno" value="<?php echo $row['id']; ?>">
            <button class="btn btn-danger" name="delete">Delete</button>
        </form>
     </td>
     
     <?php
        echo "</tr>";
    }
} else {
    echo "Error in query: " . mysqli_error($conn); 
}
?>
											
										</tbody>
									</table>
							</div>
							</div>
						</div>
            
          </div>
        </div>
        
       <?php } ?>
        
        
      </div>
    </div>
  </div>
</div></div></div></div></main>
   

<!-- Essential javascripts for application to work-->
    <script src="js/jquery-3.2.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>


</body>
