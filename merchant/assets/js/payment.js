function form_create(method,action,StringArray,submit=false){
var form = document.createElement("form");
document.body.appendChild(form);
form.method = method;
form.action = action;
for (var key in StringArray) {
var input = document.createElement("input"); 
input.name = key;
input.type = "hidden";
input.value = StringArray[key];
form.appendChild(input);
}

if(submit==true){
form.submit(); 
}
}

function countdown(elm,minute,second,url) {
document.getElementById(elm).innerHTML =minute + ":" + second; startTimer();

function startTimer() {
  var presentTime = document.getElementById(elm).innerHTML;
  var timeArray = presentTime.split(/[:]+/);
  var m = timeArray[0];
  var s = checkSecond((timeArray[1] - 1));
  if(s==59){m=m-1}
  if(m<0){
      Swal.fire("Transaction Timeout!", '', 'error');
      window.location.href = url;
  }
  document.getElementById(elm).innerHTML =
    m + ":" + s;
  //console.log(m)
  setTimeout(startTimer, 1000);
}

function checkSecond(sec) {
  if (sec < 10 && sec >= 0) {sec = "0" + sec}; // add zero in front of numbers < 10
  if (sec < 0) {sec = "59"};
  return sec;
}
}


function check_payment_status(host_url,csrf_token){
setTimeout(function(){
$.ajax({
    url: `${host_url}/order6/payment-status`, 
    type: "POST",
    data: {order_id:csrf_token},
    headers: {csrf_token:csrf_token},
    success: function(response){
        if(response.status=="PENDING"){
          check_payment_status(host_url,csrf_token);
        }else{
          window.location.reload();   
        }
    },
	error: function (error) {
		check_payment_status(host_url,csrf_token);
	}
    
});
}, 1000);
}



