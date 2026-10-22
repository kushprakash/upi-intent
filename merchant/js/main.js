(function () {
	"use strict";

	var treeviewMenu = $('.app-menu');

	// Toggle Sidebar
	$('[data-toggle="sidebar"]').click(function(event) {
		event.preventDefault();
		$('.app').toggleClass('sidenav-toggled');
	});

	// Activate sidebar treeview toggle
	$("[data-toggle='treeview']").click(function(event) {
		event.preventDefault();
		if(!$(this).parent().hasClass('is-expanded')) {
			treeviewMenu.find("[data-toggle='treeview']").parent().removeClass('is-expanded');
		}
		$(this).parent().toggleClass('is-expanded');
	});

	// Set initial active toggle
	$("[data-toggle='treeview.'].is-expanded").parent().toggleClass('is-expanded');

	//Activate bootstrip tooltips
	$("[data-toggle='tooltip']").tooltip();
	
	$(window).on('load', function() {
    // Hide the loader
    $('#loading_ajax').fadeOut();
});

function popup(status , title , msg){
    Swal.fire({
      icon: status,
      title: title,
      text: msg,
    });
}


// auth user change mode js code here
$(document).on('click','.changeusrpgmodebtn',function(e) {
	let mode = $(this).data("mode");
	let pgtoupi = true;
	e.preventDefault();
	$("#loading_ajax").show();
	$.ajax({
		url: 'backend/MerchantAuthController.php',
		type: 'POST',
		data: {
			pgtoupi,
			mode,
			changepgmode: true
		},
		success: function(data, status) {
			$("#loading_ajax").hide();
			let rslt = JSON.parse(data);
			if (rslt.rescode == 200) {
				window.location.href = siteUrl + "/imbpro/dashboard";
			}else if (rslt.rescode == 111) {
				window.location.href = siteUrl + "/imbpg/dashboard";
			} else {
				Swal.fire({
					icon: "error",
					title: "OOPS..!",
					button: "Upgrade",
					text: rslt.msg,
				}).then(() => {
					window.location.href = siteUrl + "/merchant/subscription?stype=prosubs";
				});
			}

		},
		error: function(err) {
			$("#loading_ajax").hide();
			popup('error', 'OOPS..!', "some internel error occured we are fixing it");
		}
	});

});

})();

