//RECHPAY INFOTECH

function getCurentFileName(){
    var pagePathName= window.location.pathname;
    return pagePathName.substring(pagePathName.lastIndexOf("/") + 1);
}


function loader(value){
if(value=="show"){
swal.fire({html: '<br><img src="assets/img/loading.gif"><br><br><h5>Loading...</h5>',showConfirmButton: false});   
}else if(value=="hide"){
$(".swal2-container").css('display','none');
$("body").removeClass("swal2-shown swal2-height-auto");
$("body").css('padding-right','0');
}    
}

function copyToClipboard(element) {
  var $temp = $("<input>");
  $("body").append($temp);
  $temp.val($(element).text()).select();
  document.execCommand("copy");
  alert("Copied Successfully!");
  $temp.remove();
}

function search_txn(from_date,to_date,search_input='',utr_number=''){
loader("show");  
$.ajax({
    url: 'ajax_transactions',
    type: 'POST',
    data: {search:true,from_date:from_date,to_date:to_date,search_input:search_input,utr_number:utr_number},
    success: function( response, textStatus, jQxhr ){
    loader("hide");
    var table = $('#dataTable').DataTable();
    table.rows().remove().draw();
    var sl = 1;
    $.each(response, function(key, value) {
    table.row.add([
          "<b>"+sl+"</b>",
          value['username'],
          value['txn_id'],
          value['txn_date'],
          value['merchant_name'],
          value['customer_name'],
          value['txn_note'],
          value['bank_orderid'],
          value['utr_number'],
          value['client_orderid'],
          "₹"+value['txn_amount'],
          `<span class="badge ${value['status']}">${value['status']}</span>`,
          `<a href="invoice/${value['invoice_id']}" target="blank"><span class="badge badge-primary"><i class="la la-print"></i> Print</span></a>`,
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

