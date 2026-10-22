function get_delete(plan_id){
Swal.fire({
  title: 'Are you sure you want to delete this plan?',
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
    element1.name="plan_id"
    element1.value = plan_id;
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

function get_update(base64){
var json = atob(base64);
var results = JSON.parse(json);
if(results.plan_id>0){ 
swal.fire({html: '<div style="overflow: hidden;" class="text-left mt-2"> <h6 class="mb4">Update Plan</h6> <hr /> <form class="row mb-4" method="POST" action=""> <input type="hidden" name="plan_id" value="'+results.plan_id+'" required/> <div class="col-md-6 mb-3"><label>Name</label> <input type="text" name="name" placeholder="Name" value="'+results.name+'"  class="form-control form-control-sm" required/></div> <div class="col-md-6 mb-3"> <label>Type</label> <select name="type" id="type" class="form-control form-control-sm"> <option value="1 Month">1 Month</option> <option value="1 Year">1 Year</option> </select> </div> <div class="col-md-6 mb-3"><label>Transactions Limit</label> <input type="number" name="limit" value="'+results.limit+'" placeholder="Transactions Limit" class="form-control form-control-sm" required/></div> <div class="col-md-6 mb-3"><label>Account limit</label> <input type="number" name="account_limit"value="'+results.account_limit+'"  placeholder="Account limit" class="form-control form-control-sm" required/></div> <div class="col-md-6 mb-3"><label>Amount</label> <input type="number" name="amount" placeholder="Amount" value="'+results.amount+'" class="form-control form-control-sm" required /></div> <div class="col-md-6 mb-3"> <label>Status</label> <select name="status" id="status" class="form-control form-control-sm"> <option value="Active" selected="">Active</option> <option value="InActive">InActive</option> </select> </div> <div class="col-md-12 mb-3"><button type="submit" name="update" class="btn btn-primary btn-sm btn-block">Save</button></div> </form> </div>',showConfirmButton: false});
$("#type").val(results.type);  
$("#status").val(results.status);
}else{
Swal.fire('Plan Not Found!', '', 'info'); 
}
}

function get_create(){
swal.fire({html: '<div style="overflow: hidden;" class="text-left mt-2"> <h6 class="mb4">Create Plan</h6> <hr /> <form class="row mb-4" method="POST" action=""> <div class="col-md-6 mb-3"><label>Name</label> <input type="text" name="name" placeholder="Name" class="form-control form-control-sm" required/></div> <div class="col-md-6 mb-3"> <label>Type</label> <select name="type" class="form-control form-control-sm"> <option value="1 Month">1 Month</option> <option value="1 Year">1 Year</option> </select> </div> <div class="col-md-6 mb-3"><label>Transactions Limit</label> <input type="number" name="limit" placeholder="Transactions Limit" class="form-control form-control-sm" required/></div> <div class="col-md-6 mb-3"><label>Account limit</label> <input type="number" name="account_limit" placeholder="Account limit" class="form-control form-control-sm" required/></div> <div class="col-md-6 mb-3"><label>Amount</label> <input type="number" name="amount" placeholder="Amount" class="form-control form-control-sm" required /></div> <div class="col-md-6 mb-3"> <label>Status</label> <select name="status" class="form-control form-control-sm"> <option value="Active" selected="">Active</option> <option value="InActive">InActive</option> </select> </div> <div class="col-md-12 mb-3"><button type="submit" name="create" class="btn btn-primary btn-sm btn-block">Create</button></div> </form> </div>',showConfirmButton: false});
}