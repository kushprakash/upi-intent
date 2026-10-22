function sdk_response(txnStatus, orderId, txnId){
setTimeout(function(){
	try {
		if(window.Interface){
			window.Interface.paymentResponse(txnStatus, orderId, txnId);
			return;
		}
	} catch (error) {
		console.error("[Error] Android Webview Interface not found.");
	}
}, 500);
}


function sdk_error(errorMessage){
setTimeout(function(){
	try {
		if(window.Interface){
			window.Interface.paymentError(errorMessage);
			return;
		}
	} catch (error) {
		console.error("[Error] Android Webview Interface not found.");
	}
}, 500);	
}