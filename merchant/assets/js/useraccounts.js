function get_delete(user_id){
Swal.fire({
  title: 'Are you sure you want to delete this account?',
  showDenyButton: true,
  showCancelButton: false,
  confirmButtonText: 'Yes',
  denyButtonText: 'No',
}).then((result) => {
  if (result.isConfirmed) {
	var form = document.createElement("form");
	document.body.appendChild(form);
	form.method = "POST";
	form.action = location.href;
	var element1 = document.createElement("input");         
    element1.name="user_id"
    element1.value = user_id;
    element1.type = 'hidden'
    form.appendChild(element1);
	var element2 = document.createElement("input");         
    element2.name="delete"
    element2.value = true;
    element2.type = 'hidden'
    form.appendChild(element2);
	form.submit();
  } else if (result.isDenied) {
    //Swal.fire('Good your account is safe', '', 'info');
  }
});	

}


function get_password(password){
Swal.fire('Password : '+password);
}

function get_update(user_id){
loader("show");  
$.ajax({
    url: 'ajax_usersaccounts',
    type: 'POST',
    data: {get_update:true,user_id:user_id},
    success: function( response, textStatus, jQxhr ){
    loader("hide");
    swal.fire({html: response.results,showConfirmButton: false}); 
    },
    error: function( jqXhr, textStatus, errorThrown ){
        loader("hide");
        console.log( errorThrown );
    }
});
}

function get_create(){
  swal.fire({html: '<div style="overflow: hidden;" class="text-left mt-2"> <h6 class="mb4">Create User Account</h6> <hr /> <form class="row mb-4" method="POST" action=""> <div class="col-md-6 mb-2"><label>Username</label> <input type="number" name="username" placeholder="Enter Username" class="form-control form-control-sm" onkeypress="if(this.value.length==10) return false;" required /></div> <div class="col-md-6 mb-2"><label>Password</label> <input type="text" name="password" placeholder="Enter Password" class="form-control form-control-sm" required /></div> <div class="col-md-6 mb-2"><label>Mobile Number</label> <input type="number" name="mobile" placeholder="Enter Mobile Number" class="form-control form-control-sm" onkeypress="if(this.value.length==10) return false;" required /></div> <div class="col-md-6 mb-2"><label>Email Address</label> <input type="text" name="email" placeholder="Enter Email Address" class="form-control form-control-sm" required /></div> <div class="col-md-6 mb-2"><label>Name</label> <input type="text" name="name" placeholder="Enter Name" class="form-control form-control-sm" required /></div> <div class="col-md-6 mb-2"><label>Company</label> <input type="text" name="company" placeholder="Enter Company" class="form-control form-control-sm" required /></div> <div class="col-md-6 mb-2"><label>PAN Number</label> <input type="text" name="pan" placeholder="Enter PAN Number" class="form-control form-control-sm" onkeypress="if(this.value.length==10) return false;" required /></div> <div class="col-md-6 mb-2"> <label>Aadhaar Number</label> <input type="number" name="aadhaar" placeholder="Enter Aadhaar Number" class="form-control form-control-sm" onkeypress="if(this.value.length==12) return false;" required /> </div> <div class="col-md-12 mb-2"><label>Location</label> <input type="text" name="location" placeholder="Enter Location" class="form-control form-control-sm" required /></div> <div class="col-md-6 mb-2"> <label>Role</label> <select name="role" class="form-control form-control-sm" required> <option value="User" selected="">User</option> <option value="Admin">Admin</option> </select> </div> <div class="col-md-6 mb-2"> <label>Status</label> <select name="status" class="form-control form-control-sm" required> <option value="Active" selected="">Active</option> <option value="InActive">InActive</option> </select> </div> <div class="col-md-12 mb-2 mt-2"><button type="submit" name="create" class="btn btn-primary btn-sm btn-block">Create</button></div> </form> </div>',showConfirmButton: false}); 
}


function search_users(search_input=''){
loader("show");  
$.ajax({
    url: 'ajax_usersaccounts',
    type: 'POST',
    data: {get_search:true,search_input:search_input},
    success: function( response, textStatus, jQxhr ){
    loader("hide");
    var table = $('#dataTable').DataTable();
    table.rows().remove().draw();
    var sl = 1;
    $.each(response, function(key, value) {
    table.row.add([
          "<b>"+sl+"</b>",
          value['username'],
          value['role'],
          value['name'],
          value['company'],
          value['plan'],
          value['expire_date'],
          value['token'],
          value['create_date'],
          value['status'],
          value['action']
        ]).draw();
    sl++
    });
    },
    error: function( jqXhr, textStatus, errorThrown ){
        loader("hide");
        console.log( errorThrown );
    }
});
}